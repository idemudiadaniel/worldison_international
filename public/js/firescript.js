const whatsappNumber = "2348130826625"; // Change to your WhatsApp number

// Show popup
document.querySelectorAll(".book-now-btn").forEach((btn) => {
  btn.addEventListener("click", function (e) {
    e.preventDefault();
    document.getElementById("bookingPopup").style.display = "block";
  });
});

// Close popup
function closeBooking() {
  document.getElementById("bookingPopup").style.display = "none";
}

// Service Dropdown Toggle
function toggleServiceList() {
  const list = document.getElementById("serviceList");
  list.style.display = list.style.display === "block" ? "none" : "block";
}

// Update Service Dropdown text
document
  .querySelectorAll('#serviceList input[type="checkbox"]')
  .forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const selected = Array.from(
        document.querySelectorAll('#serviceList input[type="checkbox"]:checked')
      ).map((cb) => cb.value);
      document.getElementById("serviceDropdown").textContent =
        selected.length > 0 ? selected.join(", ") : "Select services...";
    });
  });

// Urgent checkbox hides date
document
  .getElementById("urgentCheckbox")
  .addEventListener("change", function () {
    document.getElementById("serviceDate").style.display = this.checked
      ? "none"
      : "block";
  });

// Form submit
document.getElementById("bookingForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const name = document.getElementById("clientName").value;
  const selectedServices = Array.from(
    document.querySelectorAll('#serviceList input[type="checkbox"]:checked')
  ).map((cb) => cb.value);
  const location = document.getElementById("location").value;
  const email = document.getElementById("email").value;
  const urgent = document.getElementById("urgentCheckbox").checked;
  const date = urgent ? "Urgent" : document.getElementById("serviceDate").value;
  const referenceId = "REF" + Date.now();

  const message = `Booking Reference: ${referenceId}%0AName: ${name}%0AServices: ${selectedServices.join(
    ", "
  )}%0ALocation: ${location}%0AEmail: ${email}%0ADate/Time: ${date}`;
  window.open(`https://wa.me/${whatsappNumber}?text=${message}`, "_blank");

  closeBooking();
});
