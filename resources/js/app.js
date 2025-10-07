import './bootstrap';
import Sortable from "sortablejs";
import axios from "axios";
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// Toast reusable
const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: (t) => {
        t.addEventListener('mouseenter', Swal.stopTimer)
        t.addEventListener('mouseleave', Swal.resumeTimer)
    },
});

document.addEventListener("DOMContentLoaded", () => {

    // --- Sortable Kanban ---
    document.querySelectorAll(".kanban-items").forEach(container => {
        new Sortable(container, {
            group: "shared",
            animation: 150,
            ghostClass: "sortable-ghost",
            draggable: ".kanban-item",
            emptyInsertThreshold: 10,

            onEnd: async (evt) => {
                const item = evt.item;
                const saleId = item.dataset.id;
                const newStatus = evt.to.id;

                // Ambil nama project & customer dari dataset HTML
                const projectName = item.querySelector(".flex.items-center span")?.innerText ?? "Project";
const customerName = item.querySelector(".text-sm")?.innerText.replace('👤 ', '') ?? "Customer";

                try {
                    await axios.post(`/sales/${saleId}/update-status`, {
                        status: newStatus
                    }, {
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    // Update empty message
                    const toEmptyMsg = evt.to.querySelector(".empty");
                    if (toEmptyMsg) toEmptyMsg.remove();

                    if (evt.from.querySelectorAll(".kanban-item").length === 0) {
                        if (!evt.from.querySelector(".empty")) {
                            const p = document.createElement("p");
                            p.classList.add("empty", "text-sm", "text-gray-400", "italic");
                            p.innerText = "Tidak ada data";
                            evt.from.appendChild(p);
                        }
                    }

                    updateBadges();

                    toast.fire({
                        icon: 'success',
                        title: `Status ${projectName} (${customerName}) dipindah ke “${newStatus}”`,
                    });

                    // 🔥 trigger Livewire refresh
                    Livewire.dispatch('sale-updated');

                } catch (error) {
                    Swal.fire({
                        title: 'Gagal menyimpan',
                        text: error?.response?.data?.message ?? error.message,
                        icon: 'error',
                    });
                }
            }
        });
    });

    // --- Search Kanban ---
    const searchInput = document.getElementById("kanban-search");
    if (searchInput) {
        searchInput.addEventListener("input", function (e) {
            const keyword = e.target.value.toLowerCase();
            document.querySelectorAll(".kanban-item").forEach(item => {
                const text = item.dataset.search ?? "";
                item.style.display = text.includes(keyword) ? "" : "none";
            });
        });
    }

    // --- Card Actions (Edit Modal) ---
    // Tidak perlu handle wire:click manual
    // Tombol Edit sudah otomatis handle oleh Livewire
});

// --- Helper: Update badge count ---
function updateBadges() {
    document.querySelectorAll(".kanban-column").forEach(column => {
        const count = column.querySelectorAll(".kanban-item").length;
        const badge = column.closest(".fi-section")?.querySelector(".fi-badge, .filament-badge, [class*='badge']");
        if (badge) badge.textContent = count;
    });
}
