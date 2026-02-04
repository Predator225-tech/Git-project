package com.softphone.asterisk.cli;

import com.softphone.asterisk.AsteriskSoftPhoneClient;
import java.util.Scanner;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Client CLI intégré SoftPhone + Asterisk
 */
public class AsteriskCliClient {
    private static final Logger logger = LoggerFactory.getLogger(AsteriskCliClient.class);
    
    private AsteriskSoftPhoneClient client;
    private Scanner scanner;
    private boolean running = true;

    public AsteriskCliClient() {
        this.scanner = new Scanner(System.in);
    }

    public void start() {
        System.out.println("\n╔═══════════════════════════════════════════╗");
        System.out.println("║  SoftPhone + Asterisk Integration - CLI   ║");
        System.out.println("╚═══════════════════════════════════════════╝\n");

        // Configuration SoftPhone
        System.out.print("Extension SIP: ");
        String extension = scanner.nextLine().trim();

        System.out.print("Serveur SoftPhone (défaut: localhost): ");
        String softphoneServer = scanner.nextLine().trim();
        if (softphoneServer.isEmpty()) softphoneServer = "localhost";

        System.out.print("Port UDP SoftPhone (défaut: 10000): ");
        String portStr = scanner.nextLine().trim();
        int udpPort = 10000;
        try {
            if (!portStr.isEmpty()) {
                udpPort = Integer.parseInt(portStr);
            }
        } catch (NumberFormatException e) {
            System.out.println("Port invalide, utilisation de 10000");
        }

        // Configuration Asterisk
        System.out.println("\nConfiguration Asterisk:");
        System.out.print("Serveur Asterisk (défaut: localhost): ");
        String asteriskHost = scanner.nextLine().trim();
        if (asteriskHost.isEmpty()) asteriskHost = "localhost";

        System.out.print("Port Asterisk AMI (défaut: 5038): ");
        String asteriskPortStr = scanner.nextLine().trim();
        int asteriskPort = 5038;
        try {
            if (!asteriskPortStr.isEmpty()) {
                asteriskPort = Integer.parseInt(asteriskPortStr);
            }
        } catch (NumberFormatException e) {
            System.out.println("Port invalide, utilisation de 5038");
        }

        System.out.print("Utilisateur Asterisk (défaut: admin): ");
        String asteriskUser = scanner.nextLine().trim();
        if (asteriskUser.isEmpty()) asteriskUser = "admin";

        System.out.print("Mot de passe Asterisk: ");
        String asteriskPass = scanner.nextLine().trim();

        // Connexion
        System.out.println("\n[INFO] Connexion au système...");
        client = new AsteriskSoftPhoneClient(extension, softphoneServer, udpPort,
                                            asteriskHost, asteriskPort, 
                                            asteriskUser, asteriskPass);
        
        if (!client.connect()) {
            System.out.println("[ERREUR] Impossible de se connecter");
            return;
        }

        System.out.println("[OK] Connecté à SoftPhone + Asterisk!");
        System.out.println("\nCommandes disponibles:");
        System.out.println("  call <number> <context>  - Appeler un numéro");
        System.out.println("  help                     - Afficher l'aide");
        System.out.println("  quit                     - Quitter\n");

        // Boucle de commandes
        while (running) {
            System.out.print("> ");
            String command = scanner.nextLine().trim();
            processCommand(command);
        }

        client.disconnect();
    }

    private void processCommand(String command) {
        String[] parts = command.split(" ", 3);
        String action = parts[0].toLowerCase();

        switch (action) {
            case "call":
                if (parts.length < 3) {
                    System.out.println("[ERREUR] Usage: call <number> <context>");
                    System.out.println("Exemple: call 100 from-internal");
                } else {
                    String targetNumber = parts[1].trim();
                    String context = parts[2].trim();
                    client.makeCall(targetNumber, context);
                    System.out.println("[INFO] Appel vers " + targetNumber);
                }
                break;
            case "help":
                System.out.println("Commandes:");
                System.out.println("  call <number> <context> - Appeler via Asterisk");
                System.out.println("  quit                    - Quitter");
                break;
            case "quit":
                running = false;
                System.out.println("[INFO] Déconnexion...");
                break;
            case "":
                // Commande vide
                break;
            default:
                System.out.println("[ERREUR] Commande inconnue: " + action);
        }
    }

    public static void main(String[] args) {
        AsteriskCliClient client = new AsteriskCliClient();
        client.start();
    }
}
