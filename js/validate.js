document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registrationForm");
  if (!form) return;

  form.addEventListener("submit", (e) => {
    let valid = true;
    let errorMessages = [];

    const requiredFields = form.querySelectorAll("input[required], textarea[required], select[required]");
    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        valid = false;
        errorMessages.push(`${getLabelText(field)} is required.`);
      }
    });

    const email = form.querySelector('input[type="email"]');
    if (email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value.trim())) {
      valid = false;
      errorMessages.push("Please enter a valid email address.");
    }

    const password = form.querySelector('input[type="password"]');
    if (password && password.value.length < 6) {
      valid = false;
      errorMessages.push("Password must be at least 6 characters long.");
    }

    const profilePic = form.querySelector('input[name="profile_pic"]');
    if (profilePic && profilePic.files.length > 0) {
      const file = profilePic.files[0];
      if (!file.type.startsWith("image/")) {
        valid = false;
        errorMessages.push("Profile picture must be an image.");
      }
    }

    const achievementFiles = form.querySelectorAll('input[name="achievement_image[]"]');
    achievementFiles.forEach((fileInput, i) => {
      if (fileInput.files.length > 0 && !fileInput.files[0].type.startsWith("image/")) {
        valid = false;
        errorMessages.push(`Achievement ${i + 1} proof must be an image.`);
      }
    });

    if (!valid) {
      e.preventDefault();
      alert("Please fix the following errors:\n\n" + errorMessages.join("\n"));
    }
  });

  function getLabelText(field) {
    const label = field.closest("label");
    return label ? label.textContent.split(":")[0] : field.name;
  }
});
