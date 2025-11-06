import './bootstrap';


// 🔄 Spinner toàn màn hình
window.toggleLoadingSpinner = function (show = true) {
    const spinner = document.getElementById("loadingSpinner");
    if (!spinner) return;

    spinner.classList.toggle("hidden", !show);
    spinner.classList.toggle("flex", show);
};

// ✅ Toast đẹp toàn cục
window.showToast = function (type = "success", message = "Thao tác thành công!") {
    const colors = {
        success: { bg: "#f0fdf4", text: "#064e3b", border: "#10b981" },
        error: { bg: "#fef2f2", text: "#7f1d1d", border: "#ef4444" },
        warning: { bg: "#fffbeb", text: "#78350f", border: "#f59e0b" },
        info: { bg: "#eff6ff", text: "#1e3a8a", border: "#3b82f6" },
    };
    const c = colors[type] || colors.info;

    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: "transparent",
        iconColor: c.border,
        customClass: {
            popup: "swal2-modern-toast",
            title: "swal2-modern-title",
            timerProgressBar: "swal2-modern-progress",
            icon: "swal2-modern-icon",
        },
        didOpen: (toast) => {
            const style = document.createElement("style");
            style.textContent = `
                @keyframes slideInBounce {
                    0% { transform: translateX(400px); opacity: 0; }
                    60% { transform: translateX(-20px); opacity: 1; }
                    80% { transform: translateX(10px); }
                    100% { transform: translateX(0); }
                }
                @keyframes progressGlow {
                    0%,100% { box-shadow: 0 0 5px ${c.border}80; }
                    50% { box-shadow: 0 0 15px ${c.border}cc; }
                }
                .swal2-modern-toast {
                    animation: slideInBounce .6s cubic-bezier(.34,1.56,.64,1);
                    border-radius: 14px !important;
                    box-shadow: 0 8px 24px rgba(0,0,0,.08);
                    background: linear-gradient(135deg,#ffffff 0%,${c.bg} 100%) !important;
                    border: 1.5px solid ${c.border}66 !important;
                    padding: 16px 22px !important;
                    display: flex !important;
                    align-items: center !important;
                    overflow: hidden !important;
                    backdrop-filter: blur(10px);
                }
                .swal2-modern-title {
                    margin-left: 12px !important;
                    font-size: 15px !important;
                    font-weight: 600 !important;
                    color: ${c.text} !important;
                }
                .swal2-modern-progress {
                    height: 4px !important;
                    border-radius: 9999px !important;
                    background: linear-gradient(90deg,${c.border},${c.border}aa,${c.border});
                    background-size: 200% 100%;
                    animation: progressGlow 2s ease-in-out infinite, gradientMove 3s linear infinite;
                }
                @keyframes gradientMove {
                    0% { background-position: 200% center; }
                    100% { background-position: -200% center; }
                }
            `;
            document.head.appendChild(style);
        },
    });

    Toast.fire({ icon: type, title: message });
};