# tp_note_rendu
 
Ce programme n'est pas encore fonctionnel, la partie authentification n'a pas été terminée.

Pour lancer le programme :
- Créer un serveur web et BDD avec XAMPP
- Aller dans le dossier du programme et renseigner les informations de connexion à la base de données dans ".env"
- Créer la base de données en faisant la commande php bin/console doctrine:database:create
- Lancer le serveur en effectuant la commande "symfony server:start"

Pour utiliser le programmme :
Vous pouvez utiliser POSTMAN en renseignant l'url "http://localhost:8000", puis les routes pour chacune des fonctions du programme

Connexion :
Vous pouvez vous connecter à un compte de la BDD en allant sur http://localhost:8000/api/login" POST et en rentrant un json avec {
    "email": "",
    "password": ""
}


LORSQUE VOUS VOUS ËTES CONNECTE:

EN TANT QU'USER :

Mettre à jour son profil :

Vous pouvez mettre à jour votre profil en allant sur http://localhost:8000/api/users/profile PUT et en rentrant un json avec {
    "name": "",
    "email": "",
    "phoneNumber": ""
}

Créer une réservation :

Vous pouvez créer une réservation en allant sur http://localhost:8000/api/reservations POST et en rentrant un json avec {
    "date": "",
    "timeSlot": "",
    "eventName": ""
}

Voir les réservations à venir :

Vous pouvez voir les réservations à venir en allant sur http://localhost:8000/api/reservations/future GET


Voir les anciennes résevations :

Vous pouvez voir l'historique de vos réservations en allant sur http://localhost:8000/api/reservations/history GET


EN TANT QU'ADMIN :

Voir la liste de tout les utilisateurs :

Vous pouvez voir tous les utilisateurs en allant sur http://localhost:8000/api/users GET


Créer un utilisateur :

Vous pouvez créer un nouvel utilisateur en allant sur http://localhost:8000/api/users POST et en rentrant un json avec {
    "email": "",
    "name": "",
    "phoneNumber": "",
    "password": ""
}

Supprimer un utilisateur :

Vous pouvez supprimer n'importe quel utilisateur sur http://localhost:8000/api/users/[id utilisateur] DELETE


Modifier les informations des utilisateurs :

Vous pouvez modifier les informations de n'importe quel utilisateur en allant sur http://localhost:8000/api/users/[id utilisateur] PUT, et en rentrant un json avec {
    "name": "",
    "email": "",
    "phoneNumber": ""
}

Voir toutes les réservations :

Vous pouvez voir toutes les réservations actuelles en allant sur http://localhost:8000/api/reservations GET

