# Système de SoftPhone avec TCP/UDP

Un système complet de gestion d'appels en local basé sur Java utilisant TCP et UDP.

## Architecture

### Composants principaux:

1. **CallServer (TCP Port 5060)**
   - Serveur de contrôle des appels
   - Gestion de l'enregistrement des utilisateurs
   - Coordination des appels
   - Utilise des threads pour gérer plusieurs clients

2. **SoftPhoneClient (TCP + UDP)**
   - Client TCP pour communiquer avec le serveur
   - Socket UDP pour la transmission audio
   - Gestion des appels entrants/sortants
   - Thread pool pour les opérations asynchrones

3. **AudioHandler**
   - Gestion de la transmission audio via UDP
   - Envoi et réception de paquets audio
   - Simulation de données audio

4. **Interface Graphique (JavaFX)**
   - Connection au serveur
   - Gestion des appels
   - Liste des utilisateurs en ligne
   - Journal d'événements

## Installation et utilisation

### Prérequis:
- Java 11+
- Maven

### Compilation:
```bash
mvn clean package
```

### Démarrage du serveur:
```bash
java -cp target/softphone-system-1.0.0.jar com.softphone.server.CallServer
```

### Démarrage du client:
```bash
java -cp target/softphone-system-1.0.0.jar com.softphone.client.gui.SoftPhoneGUI
```

## Protocole de communication

### Messages TCP (JSON):

**Enregistrement:**
```json
{
  "action": "REGISTER",
  "username": "user1",
  "ip": "192.168.1.10",
  "udpPort": 10000
}
```

**Appel:**
```json
{
  "action": "CALL",
  "targetUsername": "user2"
}
```

**Réponse:**
```json
{
  "action": "ANSWER",
  "callId": "uuid-1234"
}
```

**Raccrocher:**
```json
{
  "action": "HANGUP",
  "callId": "uuid-1234"
}
```

## Flux d'un appel

1. Utilisateur 1 se connecte et s'enregistre (TCP)
2. Utilisateur 2 se connecte et s'enregistre (TCP)
3. Utilisateur 1 initiates un appel vers Utilisateur 2 (TCP)
4. Serveur envoie notification à Utilisateur 2
5. Utilisateur 2 accepte l'appel
6. Les deux utilisateurs établissent un canal UDP pour l'audio
7. Transmission audio bidirectionnelle (UDP)
8. Utilisateur raccroché l'appel (TCP)
9. Canal audio fermé

## Amélioration futures

- Implémentation réelle de compression audio (CODEC)
- Chiffrement des données audio
- Support des conférences multi-utilisateurs
- Enregistrement des appels
- Historique des appels
- Transfert d'appels
- Mise en attente

## Notes

- Le système simule actuellement l'audio avec du bruit blanc
- Pour une implémentation complète, intégrer une bibliothèque audio comme JavaFX MediaAPI
- Tous les messages sont en JSON pour faciliter l'extensibilité
