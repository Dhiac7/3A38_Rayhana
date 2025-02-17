import cv2
import face_recognition
import os
import numpy as np

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

def verify_face():
    video_capture = cv2.VideoCapture(0)
    known_faces_folder = 'path/to/your/folder/with/known/faces'  # Modifier ce chemin
    known_faces, known_names = load_known_faces(known_faces_folder)

    while True:
        ret, frame = video_capture.read()
        face_locations = face_recognition.face_locations(frame)

        if len(face_locations) == 0:
            print("No face detected.")
            continue

        face_encodings = face_recognition.face_encodings(frame, face_locations)

        for (top, right, bottom, left), face_encoding in zip(face_locations, face_encodings):
            matches = face_recognition.compare_faces(known_faces, face_encoding)

            if True in matches:
                first_match_index = matches.index(True)
                name = known_names[first_match_index]
                print(f"Face recognized as: {name}")
                video_capture.release()
                cv2.destroyAllWindows()
                return True  # Retourne True si la reconnaissance est réussie
            else:
                print("Unknown face.")
                video_capture.release()
                cv2.destroyAllWindows()
                return False  # Retourne False si la reconnaissance échoue

if __name__ == "__main__":
    result = verify_face()
    print(result)  # Symfony récupère cette sortie