document.addEventListener("DOMContentLoaded", async () => {
    const container = document.getElementById("validate-account");
    if (!container) return;

    const token = container.dataset.token;

    const loader = document.getElementById("loader");
    const title = document.getElementById("title");
    const message = document.getElementById("message");

    const btnResend = document.getElementById("btn-resend");

    const apiUrl = import.meta.env.VITE_API_URL;

    const setUI = ({ ttl, msg, showResend = false, isError = false }) => {
        loader.classList.add("hidden");
        title.textContent = ttl;
        message.textContent = msg;

        title.classList.remove("text-success", "text-error");
        title.classList.add(isError ? "text-error" : "text-success");

        if (showResend && btnResend) btnResend.classList.remove("hidden");
    };

    try {
        // 1) Vérifier état connexion + vérification
        const resVerify = await fetch(`${apiUrl}/account/is_verify`, {
            method: "GET",
            credentials: "include",
        });

        // CAS 2 : pas connecté
        if (resVerify.status === 401) {
            setUI({
                ttl: "Connexion requise",
                msg: "Vous devez être connecté pour valider votre compte.",
                isError: true,
            });
            return;
        }

        const dataVerify = await resVerify.json();

        // CAS 4 : déjà vérifié
        if (dataVerify?.verified === true) {
            window.location.href = "/";
            return;
        }

        // Ici : connecté + non vérifié => tenter validation
        const resValidate = await fetch(`${apiUrl}/confirm/${token}`, {
            method: "POST",
            credentials: "include",
            headers: { "Content-Type": "application/json" },
        });

        const dataValidate = await resValidate.json().catch(() => null);

        // CAS 1 : OK
        if (resValidate.ok) {
            setUI({
                ttl: "Compte validé ✅",
                msg: dataValidate?.message ?? "Votre compte a été validé avec succès.",
                isError: false,
            });

            return;
        }

        // CAS 3 : token expiré/invalide + connecté
        if (dataValidate?.code === "TOKEN_EXPIRED") {
            setUI({
                ttl: "Lien expiré",
                msg: dataValidate.message,
                showResend: true,
                isError: true,
            });
            return;
        }

        // Si token pas trouvé: compte déjà validé ou lien invalide
        if (dataValidate?.code === "ALREADY_VERIFIED") {
            window.location.href = "/";
            return;
        }

        // fallback erreur
        setUI({
            ttl: "Erreur",
            msg: dataValidate?.message ?? "Impossible de valider le compte.",
            showResend: true,
            isError: true,
        });
    } catch (e) {
        setUI({
            ttl: "Erreur réseau",
            msg: "Impossible de contacter le serveur.",
            showResend: false,
            isError: true,
        });
    }
});
