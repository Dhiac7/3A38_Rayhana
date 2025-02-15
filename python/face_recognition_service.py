import cv2
import face_recognition
import os
import numpy as np
import time

# Function to load known faces and their corresponding names from a folder
def load_known_faces(folder_path):
    known_faces = []
    known_names = []

    for file_name in os.listdir(folder_path):
        if file_name.endswith(".jpg") or file_name.endswith(".png"):
            name = os.path.splitext(file_name)[0]
            image_path = os.path.join(folder_path, file_name)
            face_image = face_recognition.load_image_file(image_path)
            face_encoding = face_recognition.face_encodings(face_image)[0]
            known_faces.append(face_encoding)
            known_names.append(name)

    return known_faces, known_names

# Function to recognize faces using the camera
def recognize_faces(known_faces, known_names):
    video_capture = cv2.VideoCapture(0)

    start_time = time.time()
    unknown_face_detected_time = None

    while True:
        ret, frame = video_capture.read()

        face_locations = face_recognition.face_locations(frame)

        # Check if no faces are detected and more than 5 seconds have passed
        if len(face_locations) == 0 and time.time() - start_time > 5:
            print("No faces detected for 5 seconds. Exiting.")
            break

        face_encodings = face_recognition.face_encodings(frame, face_locations)

        for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):
            # Convert face_encoding to a NumPy array with float64 data type
            face_encoding = np.array(face_encoding, dtype=np.float64)

            matches = face_recognition.compare_faces(known_faces, face_encoding)

            name = "Unknown"  # Default to "Unknown" if no match is found

            if True in matches:
                first_match_index = matches.index(True)
                name = known_names[first_match_index]

                # Print the recognized name
                print("Detected:", name)

                # Take action based on the recognized name
                if name != "Unknown":
                    # Do something when a known face is recognized (e.g., terminate the video feed)
                    cv2.destroyAllWindows()
                    video_capture.release()
                    exit()
                else:
                    # Reset the timer if an unknown face is detected
                    unknown_face_detected_time = time.time()
            else:
                unknown_face_detected_time = None

            cv2.rectangle(frame, (left, top), (right, bottom), (0, 255, 0), 2)
            font = cv2.FONT_HERSHEY_DUPLEX
            cv2.putText(frame, name, (left + 6, bottom - 6), font, 0.5, (255, 255, 255), 1)

        cv2.imshow('Video', frame)

        # If an unknown face has been detected for more than 5 seconds, prompt for manual login
        if unknown_face_detected_time is not None and time.time() - unknown_face_detected_time > 5:
            print("Unknown face detected for 5 seconds. Prompt for manual login.")
            # Call a function to prompt the user to enter name and password manually
            # Here you can implement the logic to display a dialog box or any other interface for manual login

        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    video_capture.release()
    cv2.destroyAllWindows()

# Set the path to the folder containing known faces (current folder where the script is located)
known_faces_folder = os.path.dirname(os.path.abspath(__file__))

# Load known faces and names
known_faces, known_names = load_known_faces(known_faces_folder)

# Start facial recognition using the camera
recognize_faces(known_faces, known_names)
