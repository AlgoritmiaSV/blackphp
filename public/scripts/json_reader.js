document.addEventListener("DOMContentLoaded", function() {
  const fileInput = document.querySelector('input[type="file"][name="json"]');
  let currentJson = {}; // store loaded JSON

  fileInput.addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
      try {
        currentJson = JSON.parse(e.target.result);

        // Populate form fields
        document.querySelectorAll("[data-field]").forEach(input => {
          const fieldPath = input.getAttribute("data-field");
          const value = getValueByPath(currentJson, fieldPath);
          if (value !== undefined) {
            input.value = value;
          }
        });
      } catch (err) {
        console.error("Invalid JSON file", err);
        Swal.fire("Error", "The uploaded file is not valid JSON.", "error");
      }
    };
    reader.readAsText(file);
  });

  // Export button handler
  const exportBtn = document.querySelector('button[type="button"]');
  exportBtn.addEventListener("click", function() {
    // Update JSON with current form values
    document.querySelectorAll("[data-field]").forEach(input => {
      const fieldPath = input.getAttribute("data-field");
      setValueByPath(currentJson, fieldPath, input.value);
    });

    // Create downloadable file
    const blob = new Blob([JSON.stringify(currentJson, null, 2)], { type: "application/json" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = "export.json";
    a.click();

    URL.revokeObjectURL(url);
  });

  // Helper to resolve nested keys like "customer.name"
  function getValueByPath(obj, path) {
    return path.split(".").reduce((acc, key) => {
      return acc && acc[key] !== undefined ? acc[key] : undefined;
    }, obj);
  }

  // Helper to set nested keys
  function setValueByPath(obj, path, value) {
    const keys = path.split(".");
    let current = obj;
    keys.forEach((key, index) => {
      if (index === keys.length - 1) {
        current[key] = value;
      } else {
        if (!current[key] || typeof current[key] !== "object") {
          current[key] = {};
        }
        current = current[key];
      }
    });
  }
});
