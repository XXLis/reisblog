
document.addEventListener("DOMContentLoaded", function () {
    // Modal-elementen en knoppen
    const modal = document.getElementById("imageModal");
    const modalImage = document.getElementById("modal-image");
    const closeButton = modal.querySelector(".close");
    const prevButton = document.getElementById("prevImage");
    const nextButton = document.getElementById("nextImage");

    // Variabelen voor het bijhouden van de huidige afbeelding en afbeeldingarray
    let currentImageIndex = 0;
    let imageArray = [];

    // Selecteer alle klikbare afbeeldingen
    const images = document.querySelectorAll(".clickable-image");
    images.forEach((image, index) => {
        // Voeg een klikgebeurtenis toe aan elke afbeelding
        image.addEventListener("click", function () {
            currentImageIndex = index; // Stel de huidige afbeeldingindex in
            imageArray = Array.from(images).map(img => img.src); // Maak een array van afbeeldingspaden
            showModalImage(imageArray[currentImageIndex]); // Toon de afbeelding in het modal
        });
    });

    // Functie om de afbeelding in het modal te tonen
    function showModalImage(imageSrc) {
        modalImage.src = imageSrc;

        // Stel de afbeeldinggrootte in op maximaal 100% van het scherm
        modalImage.style.maxWidth = '100vw';  // Maximaal 100% van de schermbreedte
        modalImage.style.maxHeight = '100vh'; // Maximaal 100% van de schermhoogte

        // Voeg de afbeelding toe aan het modal en toon het modal
        $(modal).modal('show');
    }

    // Functie om het modal te sluiten
    closeButton.addEventListener("click", function () {
        $(modal).modal('hide'); // Verberg het modal
    });

    // Functie voor de vorige afbeelding
    prevButton.addEventListener("click", function () {
        currentImageIndex = (currentImageIndex - 1 + imageArray.length) % imageArray.length;
        showModalImage(imageArray[currentImageIndex]); // Toon de vorige afbeelding
    });

    // Functie voor de volgende afbeelding
    nextButton.addEventListener("click", function () {
        currentImageIndex = (currentImageIndex + 1) % imageArray.length;
        showModalImage(imageArray[currentImageIndex]); // Toon de volgende afbeelding
    });

    // Functie voor toetsenbordnavigatie (pijlen links/rechts)
    document.addEventListener("keydown", function (event) {
        if ($(modal).hasClass('show')) { // Controleer of het modal zichtbaar is
            if (event.key === "ArrowLeft") {
                currentImageIndex = (currentImageIndex - 1 + imageArray.length) % imageArray.length;
                showModalImage(imageArray[currentImageIndex]); // Toon de vorige afbeelding met links-pijl
            } else if (event.key === "ArrowRight") {
                currentImageIndex = (currentImageIndex + 1) % imageArray.length;
                showModalImage(imageArray[currentImageIndex]); // Toon de volgende afbeelding met rechts-pijl
            }
        }
    });
});
