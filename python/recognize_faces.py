import cv2
import face_recognition
import os
import numpy as np
import sys
from PIL import Image
import time

def load_known_faces(folder_path):
    known_faces = []
    known_names = []
    
    for file_name in os.listdir(folder_path):
        if file_name.lower().endswith((".jpg", ".jpeg", ".png")):
            name = os.path.splitext(file_name)[0]
            image_path = os.path.join(folder_path, file_name)
            try:
                # Charger et convertir l'image
                face_image = Image.open(image_path)
                if face_image.mode == 'RGBA':
                    background = Image.new('RGB', face_image.size, (255, 255, 255))
                    background.paste(face_image, mask=face_image.split()[3])
                    face_image = background
                elif face_image.mode != 'RGB':
                    face_image = face_image.convert('RGB')
                
                face_image_np = np.array(face_image)
                if face_image_np.dtype != np.uint8:
                    face_image_np = face_image_np.astype(np.uint8)
                
                # Encodage des visages
                face_encodings = face_recognition.face_encodings(face_image_np)
                if len(face_encodings) == 0:
                    continue
                
                known_faces.append(face_encodings[0])
                known_names.append(name)
                
            except Exception as e:
                continue
                
    return known_faces, known_names

def recognize_faces(known_faces, known_names):
    video_capture = cv2.VideoCapture(0)
    
    if not video_capture.isOpened():
        return False  # Erreur d'accès à la caméra

    start_time = time.time()  # Démarrage du temps

    while True:
        ret, frame = video_capture.read()
        if not ret:
            break

        rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        face_locations = face_recognition.face_locations(rgb_frame)
        face_encodings = face_recognition.face_encodings(rgb_frame, face_locations)

        for face_encoding in face_encodings:
            matches = face_recognition.compare_faces(known_faces, face_encoding, tolerance=0.6)
            if True in matches:
                best_match_index = np.argmin(face_recognition.face_distance(known_faces, face_encoding))
                
                video_capture.release()
                cv2.destroyAllWindows()
                return True

        if time.time() - start_time > 10:  # Arrêt après 10 secondes
            break

    video_capture.release()
    cv2.destroyAllWindows()
    return False  # Aucun visage reconnu

if __name__ == "__main__":
    folder_path = sys.argv[1]  # Dossier contenant les images des utilisateurs connus
    known_faces, known_names = load_known_faces(folder_path)
    
    if len(known_faces) == 0:
        print("False")  # Aucun visage connu trouvé
        sys.exit(0)

    result = recognize_faces(known_faces, known_names)
    print("True" if result else "False")
