// Vérifier le statut de connexion
function checkLoginStatus() {
  let isConnected = document.body.getAttribute('data-is-connected') === 'true';
  if (isConnected) {
    return true;
  } else {
    $('#connectionModal').modal('show');
    return false;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const heartIcons = document.querySelectorAll('.heartIcon');
  
  heartIcons.forEach(heartIcon => {
    heartIcon.addEventListener('click', function() {
      if (checkLoginStatus()) {
        const toastEl = document.getElementById('myToast');
        const toast = new bootstrap.Toast(toastEl);
        if (this.classList.contains('far')) {
          this.classList.remove('far');
          this.classList.add('fas');
          this.style.setProperty('color', 'red', 'important');
          toastEl.querySelector('.toast-body').textContent = 'Logement ajouté aux favoris.';
          toast.show();
        } else {
          this.classList.remove('fas');
          this.classList.add('far');
          this.style.setProperty('color', 'white', 'important');
          toastEl.querySelector('.toast-body').textContent = 'Logement retiré des favoris.';
          toast.show();
        }
        
        // Faire disparaître le toast avec un effet d'opacité après 3 secondes
        setTimeout(() => {
          toastEl.classList.add('fade-out');
          setTimeout(() => {
            toast.hide();
            toastEl.classList.remove('fade-out');
          }, 3000);
        }, 10);
      }
    });
  });
});















//EFFET HERO BANNER (Des séjours qui font du bien, BARRE DE RECHERCHER SEJOURS)

$(document).ready(function () {
  const voyageursButton = $("#voyageurs");
  const adultsInput = $("#adults");
  const childrenInput = $("#children");
  const babiesInput = $("#babies");
  const petsInput = $("#pets");

  voyageursButton.on("click", updateVoyageursText);
  adultsInput.on("input", updateVoyageursText);
  childrenInput.on("input", updateVoyageursText);
  babiesInput.on("input", updateVoyageursText);
  petsInput.on("input", updateVoyageursText);

  // effet barre de recherche survol
  $(".search-icon").click(function () {
    if ($("#searchForm").is(":visible")) {
      $("#searchForm").collapse("hide");
    } else {
      $("#searchForm").collapse("show");
    }
  });

  // Textes à afficher
  let texts = [
    "Pour l'économie locale.",
    "Pour un dépaysement total.",
    "Pour nos belles régions.",
    "Pour vous et vos chers.",
    "Pour des moments de détente.",
  ];
  let textIndex = 0;
  let charIndex = 0;
  let isDeleting = false;
  let currentText = texts[0];

  // Obtenir l'élément
  let span = document.getElementById("dynamicText");

  // Fonction d'animation
  function animateText() {
    if (!isDeleting) {
      if (charIndex < currentText.length) {
        // Ajouter le caractère suivant du texte
        span.textContent += currentText[charIndex];
        charIndex++;
      } else {
        // Une fois que le texte a été entièrement affiché, commencer à effacer
        isDeleting = true;
      }
    } else {
      if (
        span.textContent.length > 4 ||
        (span.textContent.length == 4 && span.textContent != "Pour")
      ) {
        // Enlever le dernier caractère du texte
        span.textContent = span.textContent.slice(0, -1);
        charIndex--;
      } else {
        // Une fois que tout le texte a été effacé jusqu'à "Pour", passer au texte suivant
        isDeleting = false;
        textIndex = (textIndex + 1) % texts.length;
        currentText = texts[textIndex];
        charIndex = 4;
      }
    }
  }

  // Animer le texte à intervalles réguliers
  let interval = setInterval(animateText, 150); // Vitesse de l'animation

  // effet calendrier
  $("#confirmButton").on("click", function () {
    // Récupérer les valeurs des champs input
    var adults = parseInt($("#adults").val()) || 0;
    var children = parseInt($("#children").val()) || 0;
    var babies = parseInt($("#babies").val()) || 0;
    var pets = parseInt($("#pets").val()) || 0;

    // Vérifier si aucun adulte n'est sélectionné
    if (adults < 1) {
      adults = 1; // Définir le nombre d'adultes sur 1
      $("#adults").val(adults); // Mettre à jour la valeur de l'input
    }

    // Mettre à jour l'attribut min de l'input pour adultes
    $("#adults").attr("min", 1);

    // Calculer le nombre total de voyageurs
    var total = adults + children + babies + pets;

    // Afficher le nombre total de voyageurs
    voyageursButton.text(total > 0 ? total + " Voyageur(s)" : "Choisissez...");
  });

  // Fonction pour mettre à jour le texte relatif aux voyageurs
  function updateVoyageursText() {
    const adults = parseInt(adultsInput.val()) || 0;
    const children = parseInt(childrenInput.val()) || 0;
    const babies = parseInt(babiesInput.val()) || 0;
    const pets = parseInt(petsInput.val()) || 0;

    const totalVoyageurs = adults + children + babies + pets;

    if (totalVoyageurs === 0) {
      voyageursButton.text("2 adultes");
    } else {
      voyageursButton.text(totalVoyageurs + " voyageur(s)");
    }
  }
});

// EFFET CARDS-AVIS 2eme section
document.addEventListener("DOMContentLoaded", function () {
  const carousel2 = document.querySelector(".custom-carousel-2");
  const track2 = document.querySelector(".carousel-track-2");
  const btnPrev2 = document.querySelector(".carousel-btn-prev-2");
  const btnNext2 = document.querySelector(".carousel-btn-next-2");
  const cards = document.querySelectorAll(".carousel-card-2");
  let index2 = 0;

  function calculateCardWidth() {
    return cards[index2].getBoundingClientRect().width;
  }

  function updateCarousel() {
    const cardWidth2 = calculateCardWidth();
    track2.style.width = `${cards.length * cardWidth2}px`;
    track2.scrollLeft = index2 * cardWidth2;
  }

  function handleResize() {
    updateCarousel();
  }

  function handlePrevClick() {
    if (index2 > 0) {
      index2--;
      updateCarousel();
    }
  }

  function handleNextClick() {
    if (index2 < cards.length - 1) {
      index2++;
      updateCarousel();
    }
  }

  btnPrev2.addEventListener("click", handlePrevClick);
  btnNext2.addEventListener("click", handleNextClick);

  carousel2.addEventListener("mouseenter", () => {
    btnPrev2.classList.add("visible");
    btnNext2.classList.add("visible");
  });

  carousel2.addEventListener("mouseleave", () => {
    btnPrev2.classList.remove("visible");
    btnNext2.classList.remove("visible");
  });

  cards.forEach((card) => {
    card.addEventListener("mouseenter", () => {
      card.classList.add("card-hovered");
    });

    card.addEventListener("mouseleave", () => {
      card.classList.remove("card-hovered");
    });
  });

  window.addEventListener("resize", handleResize);
  handleResize(); // Appeler la fonction une fois au chargement de la page
});

  // Variable pour le comportement du scrolling
  let scrolling2 = false;
  let scrollingTimeout;

  // Fonction pour cacher la navbar après un certain temps
  function hideNavbar() {
    document.querySelector(".navbar").classList.add("d-none");
    scrolling2 = false;
  }

  // Fonction pour afficher la navbar lors du scrolling
  function showNavbar() {
    document.querySelector(".navbar").classList.remove("d-none");
    scrolling2 = true;
    clearTimeout(scrollingTimeout);
    scrollingTimeout = setTimeout(hideNavbar, 4000);
  }

  // Fonction pour détecter le début du scrolling
  function handleScrollStart() {
    if (!scrolling2) {
      showNavbar();
    }
  }

  // Fonction pour détecter le fin du scrolling
  function handleScrollEnd() {
    clearTimeout(scrollingTimeout);
    scrollingTimeout = setTimeout(hideNavbar, 5000);
  }

  // Fonction pour arrêter le compte à rebours lorsque la souris est sur la navbar
  function handleMouseOver() {
    clearTimeout(scrollingTimeout);
  }

  // Fonction pour reprendre le compte à rebours lorsque la souris quitte la navbar
  function handleMouseOut() {
    scrollingTimeout = setTimeout(hideNavbar, 5000);
  }

  // Ajouter des écouteurs d'événements pour détecter le début et la fin du scrolling
  window.addEventListener("scroll", handleScrollStart);
  window.addEventListener("scroll", handleScrollEnd);

  // Ajouter des écouteurs d'événements pour détecter la présence de la souris sur la navbar
  document.querySelector(".navbar").addEventListener("mouseover", handleMouseOver);
  document.querySelector(".navbar").addEventListener("mouseout", handleMouseOut);

  
  
  
