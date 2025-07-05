<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Student</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(-45deg, #0f172a, #1e3a8a, #0c4a6e, #1e293b);
      background-size: 400% 400%;
      animation: gradient 10s ease infinite;
    }

    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    select, option {
      color: black !important; /* Fix for dropdown white text */
    }
  </style>
</head>
<body class="text-white min-h-screen flex items-center justify-center">
  <div class="w-full max-w-3xl mx-auto my-10 p-6 bg-white bg-opacity-10 backdrop-blur-md rounded-xl shadow border border-white border-opacity-20">
    <h2 class="text-3xl font-bold mb-6 text-center">Student Registration</h2>

    <form id="registrationForm" action="register_submit.php" method="POST" enctype="multipart/form-data" class="space-y-6">
      <!-- Step 1: Account Info -->
      <div id="step1">
        <fieldset class="border border-white border-opacity-30 p-4 rounded-lg">
          <legend class="text-lg font-semibold mb-2">1. Account Info</legend>
          <label class="block mb-2">Username:
            <input type="text" name="username" required class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white">
          </label>
          <label class="block mb-2">Email:
            <input type="email" name="email" required class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white">
          </label>
          <label class="block mb-2">Password:
            <input type="password" name="password" required class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white">
          </label>
          <label class="block mb-2">Profile Picture:
            <input type="file" name="profile_pic" accept="image/*" required class="mt-1 block text-white">
          </label>
        </fieldset>
        <div class="text-center">
          <button type="button" onclick="nextStep(1)" class="mt-4 bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded">Next</button>
        </div>
      </div>

      <!-- Step 2: Student Info -->
      <div id="step2" class="hidden">
        <fieldset class="border border-white border-opacity-30 p-4 rounded-lg">
          <legend class="text-lg font-semibold mb-2">2. Student Info</legend>
          <label class="block mb-2">Full Name:
            <input type="text" name="full_name" required class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white">
          </label>
          <label class="block mb-2">Course:
            <input type="text" name="course" required class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white">
          </label>
          <label class="block mb-2">Year Level:
            <select name="year_level" required class="mt-1 w-full p-2 rounded bg-white bg-opacity-90 text-black">
              <option value="">Select Year</option>
              <option>1st Year</option>
              <option>2nd Year</option>
              <option>3rd Year</option>
              <option>4th Year</option>
            </select>
          </label>
          <label class="block mb-2">Address:
            <textarea name="address" required class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white"></textarea>
          </label>
          <label class="block mb-2">Gender:
            <select name="gender" required class="mt-1 w-full p-2 rounded bg-white bg-opacity-90 text-black">
              <option value="">Select Gender</option>
              <option>Male</option>
              <option>Female</option>
              <option>Other</option>
            </select>
          </label>
        </fieldset>
        <div class="text-center space-x-4">
          <button type="button" onclick="prevStep(2)" class="mt-4 bg-gray-500 hover:bg-gray-600 px-4 py-2 rounded">Back</button>
          <button type="button" onclick="nextStep(2)" class="mt-4 bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded">Next</button>
        </div>
      </div>

      <!-- Step 3: Achievements (optional) -->
      <div id="step3" class="hidden">
        <fieldset id="achievements-container" class="border border-white border-opacity-30 p-4 rounded-lg">
          <legend class="text-lg font-semibold mb-2">3. Achievements (Optional)</legend>
          <!-- Start with empty, allow user to add -->
        </fieldset>
        <div class="text-center space-x-4">
          <button type="button" onclick="prevStep(3)" class="mt-4 bg-gray-500 hover:bg-gray-600 px-4 py-2 rounded">Back</button>
          <button type="button" onclick="addAchievement()" class="mt-4 bg-green-600 hover:bg-green-700 px-4 py-2 rounded">+ Add Achievement</button>
          <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded">Submit</button>
        </div>
      </div>
    </form>
  </div>

  <script>
    function nextStep(step) {
      const currentStep = document.getElementById('step' + step);
      const inputs = currentStep.querySelectorAll('input, select, textarea');
      let isValid = true;
      inputs.forEach(input => {
        if (!input.checkValidity()) {
          input.reportValidity();
          isValid = false;
        }
      });
      if (isValid) {
        currentStep.classList.add('hidden');
        document.getElementById('step' + (step + 1)).classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    }

    function prevStep(step) {
      document.getElementById('step' + step).classList.add('hidden');
      document.getElementById('step' + (step - 1)).classList.remove('hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function addAchievement() {
      const container = document.getElementById('achievements-container');
      const group = document.createElement('div');
      group.classList.add('achievement-group', 'space-y-2');
      group.innerHTML = `
        <label class="block">Title:
          <input type="text" name="achievement_title[]" class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white">
        </label>
        <label class="block">Description:
          <textarea name="achievement_description[]" class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white"></textarea>
        </label>
        <label class="block">Proof Image:
          <input type="file" name="achievement_image[]" accept="image/*" class="mt-1 block text-white">
        </label>
        <hr class="my-4 border-white border-opacity-30">
      `;
      container.appendChild(group);
    }
  </script>
</body>
</html>
