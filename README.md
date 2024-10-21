# Reisblog Platform

## Overzicht
Reisblog Platform is een gebruiksvriendelijk systeem waarmee gebruikers hun reiservaringen kunnen delen door middel van blogposts, ondersteund door afbeeldingen. Dit platform biedt een intuïtieve interface voor het maken, bewerken en beheren van reisposts, waardoor het delen van avonturen met een breed publiek eenvoudig wordt.

## Functies
- **Gebruikersregistratie**: Gebruikers hebben de mogelijkheid om hun eigen account aan te maken, in te loggen en hun blogposts te beheren.
- **Wachtwoordherstel**: Deze functie is alleen online beschikbaar en kan getest worden via de [Reisblog Platform](https://reisblogplatform.byethost7.com) website.
- **Blogposts aanmaken**: Gebruikers kunnen eenvoudig nieuwe blogposts maken en hun verhalen delen.
- **Afbeeldingen uploaden**: Ondersteuning voor het uploaden van meerdere afbeeldingen per post, waarbij afbeeldingen automatisch worden geoptimaliseerd voor het web.
- **Beheeropties**: Gebruikers kunnen hun eigen posts bewerken of verwijderen. Admins kunnen ook alle posts beheren.
- **Afbeeldingsgalerij met navigatie**: Klik op een afbeelding om deze te vergroten, en navigeer gemakkelijk tussen de afbeeldingen met behulp van pijltjestoetsen of klikbare pijlen.
- **Responsieve interface**: Het platform is volledig responsief, wat betekent dat het op alle apparaten goed werkt, van desktop tot mobiel.

## Installatie-instructies
- Dit project kan online worden bekeken op: [Reisblog Platform](https://reisblogplatform.byethost7.com)
- **Voor lokale installatie**:
  1. Clone de repository.
  2. Importeer het `reisblog.sql` bestand in een MySQL-database.
  3. Pas de `config.php` aan met je database-instellingen.
  4. Zorg dat je server ondersteuning biedt voor PHP en MySQL. De wachtwoordhersteloptie is alleen beschikbaar in de online versie en niet lokaal getest.
  5. In de lokale versie zijn er testgebruikers beschikbaar om de functionaliteiten te verkennen:
     - **Testgebruiker**:  
       Gebruikersnaam: Test01  
       E-mail: test@gmail.com  
       Wachtwoord: test
     - **Admingebruiker**:  
       E-mail: admin@gmail.com  
       Wachtwoord: welkom

## Technologieën gebruikt
- **PHP**: Voor de server-side logica en communicatie met de database.
- **MySQL**: Voor het opslaan van gebruikers, blogposts en geüploade afbeeldingen.
- **PHPMailer**: Voor het versturen van e-mails bij wachtwoordherstel (alleen online).
- **Bootstrap**: Voor een responsieve en gebruiksvriendelijke frontend.
- **JavaScript**: Voor de dynamische interactie, zoals het vergroten van afbeeldingen en navigatie tussen afbeeldingen.

**Let op**: Alle noodzakelijke aanpassingen en correcties zijn op dit moment volledig doorgevoerd in de lokale versie van het project. De testomgeving is gebruiksklaar.
