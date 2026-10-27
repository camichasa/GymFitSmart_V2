// Página a la que se vuelve al cerrar sesión (ajustar a la ruta real del login)
const PAGINA_LOGIN = "../auth/cerrar_sesion.php";

// codigo del Carrusel: solo si existe en la pantalla
const contenedorPuntos = document.getElementById('carrusel-puntos');
if (contenedorPuntos) {
    const slides = document.querySelectorAll('.slide');
    let indice = 0;
    let temporizador;

    slides.forEach((_, i) => {
        const punto = document.createElement('button');
        punto.className = 'punto' + (i === 0 ? ' active' : '');
        punto.addEventListener('click', () => {
            mostrarSlide(i);
            reiniciarAuto();
        });
        contenedorPuntos.appendChild(punto);
    });
    const puntos = document.querySelectorAll('.punto');

    function mostrarSlide(n) {
        slides[indice].classList.remove('active');
        puntos[indice].classList.remove('active');
        indice = (n + slides.length) % slides.length;
        slides[indice].classList.add('active');
        puntos[indice].classList.add('active');
    }

    function reiniciarAuto() {
        clearInterval(temporizador);
        temporizador = setInterval(() => mostrarSlide(indice + 1), 4000);
    }

    document.getElementById('carrusel-prev').addEventListener('click', () => {
        mostrarSlide(indice - 1);
        reiniciarAuto();
    });
    document.getElementById('carrusel-next').addEventListener('click', () => {
        mostrarSlide(indice + 1);
        reiniciarAuto();
    });

    reiniciarAuto();
}

// Menú desplegable que esta en todas las pantallas
const btnMenu = document.getElementById('btn-menu');
        const btnCerrar = document.getElementById('btn-cerrar');
        const menuLateral = document.getElementById('menu-lateral');
        const overlay = document.getElementById('menu-overlay');

        function abrirMenu() {
            menuLateral.classList.add('active');
            overlay.classList.add('active');
            btnMenu.setAttribute('aria-expanded', 'true');
        }

        function cerrarMenu() {
            menuLateral.classList.remove('active');
            overlay.classList.remove('active');
            btnMenu.setAttribute('aria-expanded', 'false');
        }

        btnMenu.addEventListener('click', abrirMenu);
        btnCerrar.addEventListener('click', cerrarMenu);
        overlay.addEventListener('click', cerrarMenu);

        // Cerrar el menú con la tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') cerrarMenu();
        });

// Rutina dentro de mi rutina
const dias = document.querySelectorAll('.day-box');
const detalle = document.getElementById('detalle');
if (dias.length && detalle) {
    const rutinas = { /* ... tu objeto ... */ };

    dias.forEach((dia) => {
        dia.addEventListener('click', () => {
            const r = rutinas[dia.dataset.dia];
            detalle.innerHTML = `
                <h3>Rutina del ${r.nombre}</h3>
                <p><strong>Ejercicio:</strong> ${r.ejercicio}</p>
                <p>${r.descripcion} — <strong>Carga:</strong> ${r.carga}</p>`;
            detalle.classList.add('active');
        });
    });
}

// Descargar PDF de la rutina
const openPdfModal = document.getElementById('open-pdf-modal');
const pdfModal = document.getElementById('pdf-modal');
const confirmPdf = document.getElementById('confirm-pdf');
if (openPdfModal && pdfModal && confirmPdf) {
    openPdfModal.addEventListener('click', () => pdfModal.showModal());
    confirmPdf.addEventListener('click', () => {
        window.location.href = 'descargar_rutina_pdf.php';
        pdfModal.close();
    });
}

// Cerrar sesión
        const logoutLink = document.getElementById('logout-link');
        const logoutModal = document.getElementById('logout-modal');
        const confirmLogout = document.getElementById('confirm-logout');

        logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            cerrarMenu();
            logoutModal.showModal();
        });

        confirmLogout.addEventListener('click', () => {
            alert('Sesión cerrada con éxito');
            window.location.href = PAGINA_LOGIN;
        });

