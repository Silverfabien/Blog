document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('image_modal')
    const container = document.getElementById('zoom-container')
    const img = document.getElementById('modal-image')

    let isZoomed = false
    let lastMouseEvent = null
    const ZOOM = 2

    // Ouvrir la modal
    document.querySelectorAll('.article-content img').forEach(sourceImg => {
        sourceImg.addEventListener('click', () => {
            img.src = sourceImg.src
            isZoomed = false
            container.classList.remove('is-zoomed')
            img.style.transform = 'scale(1)'
            modal.showModal()
        })
    })

    // Toggle zoom
    container.addEventListener('click', (e) => {
        isZoomed = !isZoomed
        container.classList.toggle('is-zoomed', isZoomed)

        if (!isZoomed) {
            img.style.transform = 'scale(1)'
            return
        }

        // Si la souris n’a pas encore bougé, on prend la position du clic
        if (!lastMouseEvent) {
            lastMouseEvent = e
        }

        // Forcer l’application du zoom avec le même calcul
        container.dispatchEvent(
            new MouseEvent('mousemove', {
                clientX: lastMouseEvent.clientX,
                clientY: lastMouseEvent.clientY,
                bubbles: true
            })
        )
    })

    // Zoom qui suit la souris (STABLE)
    container.addEventListener('mousemove', (e) => {
        lastMouseEvent = e
        if (!isZoomed) return

        const rect = container.getBoundingClientRect()

        const x = (e.clientX - rect.left) / rect.width
        const y = (e.clientY - rect.top) / rect.height

        // Taille réelle de l’image zoomée
        const imgRatio = img.naturalWidth / img.naturalHeight
        const containerRatio = rect.width / rect.height

        let scaledWidth, scaledHeight

        if (imgRatio > containerRatio) {
            // Image contrainte par la largeur
            scaledWidth = rect.width * ZOOM
            scaledHeight = (rect.width / imgRatio) * ZOOM
        } else {
            // Image contrainte par la hauteur
            scaledHeight = rect.height * ZOOM
            scaledWidth = (rect.height * imgRatio) * ZOOM
        }

        // Dépassements réels
        const overflowX = Math.max(0, scaledWidth - rect.width)
        const overflowY = Math.max(0, scaledHeight - rect.height)

        // Translation autorisée uniquement si dépassement
        const translateX = overflowX
            ? (0.5 - x) * overflowX
            : 0

        const translateY = overflowY
            ? (0.5 - y) * overflowY
            : 0

        img.style.transform = `
        translate(${translateX}px, ${translateY}px)
        scale(${ZOOM})
    `
    })

    // Reset à la fermeture
    modal.addEventListener('close', () => {
        isZoomed = false
        img.style.transform = 'scale(1)'
    })
})
