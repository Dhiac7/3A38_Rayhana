#!/usr/bin/env python
import sys
import face_recognition

if len(sys.argv) < 3:
    print("error")
    sys.exit(1)

face_image_path = sys.argv[1]
known_face_path = sys.argv[2]

try:
    face_image = face_recognition.load_image_file(face_image_path)
    known_face_image = face_recognition.load_image_file(known_face_path)
except Exception as e:
    print("error")
    sys.exit(1)

face_encodings = face_recognition.face_encodings(face_image)
known_face_encodings = face_recognition.face_encodings(known_face_image)

if len(face_encodings) == 0 or len(known_face_encodings) == 0:
    print("no_face")
    sys.exit(1)

face_encoding = face_encodings[0]
known_face_encoding = known_face_encodings[0]

matches = face_recognition.compare_faces([known_face_encoding], face_encoding)
if matches[0]:
    print("match")
else:
    print("no_match")