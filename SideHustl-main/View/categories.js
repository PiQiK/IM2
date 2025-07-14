document.addEventListener('DOMContentLoaded', function () {
    const dropdownButton = document.querySelector('.dropdown-btn');
    const dropdownContent = document.querySelector('.dropdown-content');
    const closeImage = document.querySelector('.lined img'); // Access the image directly
  
    const toggleDropdown = () => {
      if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none";
      } else {
        dropdownContent.style.display = "block";
      }
    };
    
  
    dropdownButton.addEventListener('click', toggleDropdown);
  
    closeImage.addEventListener('click', function () {
      dropdownContent.style.display = "none";
    });
  });
  