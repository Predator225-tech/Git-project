package com.softphone.client.cli;

import com.softphone.client.SoftPhoneClient;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.Scanner;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Client SoftPhone en ligne de commande
 */
public class SoftPhoneCliClient {
    private static final Logger logger = LoggerFactory.getLogger(SoftPhoneCliClient.class);
    
    private SoftPhoneClient client;
    private Scanner scanner;
    private boolean running = true;

    public SoftPhoneCliClient() {
        this.scanner = new Scanner(System.in);
    }

    public void start() {
        System.out.println("\n╔════════════════════════════════════╗");
        System.out.println("║      SoftPhone Client - CLI         ║");
        System.out.println("╚════════════════════════════════════╝\n");

        // Demander les informations de connexion
        System.out.print("Entrez votre nom d'utilisateur: ");
        String username = scanner.nextLine().trim();

        System.out.print("Adresse du serveur (défaut: localhost): ");
        String server = scanner.nextLine().trim();
        if (server.isEmpty()) server = "localhost";

        System.out.print("Port UDP local (défaut: 10000): ");
        String portStr = scanner.nextLine().trim();
        int udpPort = 10000;
        try {
            if (!portStr.isEmpty()) {
                udpPort = Integer.parseInt(portStr);
            }
        } catch (NumberFormatException e) {
            System.out.println("Port invalide, utilisation de 10000");
        }

        // Connexion
        System.out.println("\n[INFO] Connexion au serveur...");
        client = new SoftPhoneClient(username, server, udpPort);
        if (!client.connect()) {
            System.out.println("[ERREUR] Impossible de se connecter au serveur");
            return;
        }

        System.out.println("[OK] Connecté!");
        System.out.println("\nCommandes disponibles:");
        System.out.println("  list     - Lister les utilisateurs en ligne");
        System.out.println("  call <user> - Appeler un utilisateur");
        System.out.println("  hangup   - Raccrocher");
        System.out.println("  quit     - Quitter\n");

        // Boucle de commandes
        while (running) {
            System.out.print("> ");
            String command = scanner.nextLine().trim();
            processCommand(command);
        }

        client.disconnect();
    }

    private void processCommand(String command) {
        String[] parts = command.split(" ", 2);
        String action = parts[0].toLowerCase();

        switch (action) {
            case "list":
                client.listUsers();
                System.out.println("[INFO] Liste envoyée au serveur");
                break;
            case "call":
                if (parts.length < 2) {
                    System.out.println("[ERREUR] Usage: call <username>");
                } else {
                    String target = parts[1].trim();
                    client.initiateCall(target);
                    System.out.println("[INFO] Appel initié vers " + target);
                }
                break;
            case "hangup":
                client.hangupCall();
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
        SoftPhoneCliClient client = new SoftPhoneCliClient();
        client.start();
    }
}
