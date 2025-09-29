// dashboard.js
document.addEventListener("DOMContentLoaded", function () {
  // Menu toggle functionality
  const menuToggle = document.getElementById("menuToggle");
  const sidebar = document.querySelector(".sidebar");

  if (menuToggle && sidebar) {
    menuToggle.addEventListener("click", function () {
      sidebar.classList.toggle("active");
    });
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", function (event) {
    if (window.innerWidth <= 992) {
      const isClickInsideSidebar = sidebar.contains(event.target);
      const isClickInsideMenuToggle = menuToggle.contains(event.target);

      if (
        !isClickInsideSidebar &&
        !isClickInsideMenuToggle &&
        sidebar.classList.contains("active")
      ) {
        sidebar.classList.remove("active");
      }
    }
  });

  // Update stats with animation
  function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
      if (!startTimestamp) startTimestamp = timestamp;
      const progress = Math.min((timestamp - startTimestamp) / duration, 1);
      const value = Math.floor(progress * (end - start) + start);
      element.textContent = value.toLocaleString();
      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    };
    window.requestAnimationFrame(step);
  }

  // Simulate loading stats with animation
  setTimeout(() => {
    const statValues = document.querySelectorAll(".stat-value");
    if (statValues.length > 0) {
      // Animate first stat (Vendas Hoje)
      if (statValues[0].textContent === "24") {
        animateValue(statValues[0], 0, 24, 1000);
      }

      // Animate third stat (Produtos em Stock)
      if (statValues[2].textContent === "1.847") {
        animateValue(statValues[2], 1500, 1847, 1500);
      }

      // Animate fourth stat (Clientes Ativos)
      if (statValues[3].textContent === "342") {
        animateValue(statValues[3], 300, 342, 1200);
      }
    }
  }, 500);

  // Add active class to nav items on click
  const navItems = document.querySelectorAll(".nav-item");
  navItems.forEach((item) => {
    item.addEventListener("click", function () {
      navItems.forEach((i) => i.classList.remove("active"));
      this.classList.add("active");

      // Close sidebar on mobile after selection
      if (window.innerWidth <= 992) {
        sidebar.classList.remove("active");
      }
    });
  });

  // Simulate real-time updates
  setInterval(() => {
    // Randomly update the "Vendas Hoje" stat
    const todaySales = document.querySelector(
      ".stat-card:nth-child(1) .stat-value"
    );
    if (todaySales) {
      const currentValue = parseInt(todaySales.textContent);
      const newValue = currentValue + Math.floor(Math.random() * 3);
      todaySales.textContent = newValue;

      // Update the percentage change
      const changeElement = document.querySelector(
        ".stat-card:nth-child(1) .stat-change"
      );
      if (changeElement) {
        const change = Math.floor(Math.random() * 5) + 1;
        changeElement.textContent = `+${change}%`;
      }
    }
  }, 30000); // Update every 30 seconds

  
});

