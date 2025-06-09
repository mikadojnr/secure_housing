import "./bootstrap"
import "../css/app.css"

// Initialize any global JavaScript functionality
document.addEventListener("DOMContentLoaded", () => {
  // Auto-hide flash messages after 5 seconds
  const flashMessages = document.querySelectorAll(".flash-message")
  flashMessages.forEach((message) => {
    setTimeout(() => {
      message.style.transition = "opacity 0.5s ease-out"
      message.style.opacity = "0"
      setTimeout(() => {
        message.remove()
      }, 500)
    }, 5000)
  })

  // Initialize tooltips
  const tooltips = document.querySelectorAll("[data-tooltip]")
  tooltips.forEach((tooltip) => {
    tooltip.addEventListener("mouseenter", function () {
      const tooltipText = this.getAttribute("data-tooltip")
      const tooltipElement = document.createElement("div")
      tooltipElement.className = "absolute z-50 px-2 py-1 text-sm text-white bg-gray-900 rounded shadow-lg"
      tooltipElement.textContent = tooltipText
      tooltipElement.id = "tooltip-" + Math.random().toString(36).substr(2, 9)

      this.appendChild(tooltipElement)

      // Position tooltip
      const rect = this.getBoundingClientRect()
      tooltipElement.style.top = rect.top - tooltipElement.offsetHeight - 5 + "px"
      tooltipElement.style.left = rect.left + rect.width / 2 - tooltipElement.offsetWidth / 2 + "px"
    })

    tooltip.addEventListener("mouseleave", function () {
      const tooltipElement = this.querySelector('[id^="tooltip-"]')
      if (tooltipElement) {
        tooltipElement.remove()
      }
    })
  })

  // Initialize mobile menu toggle
  const mobileMenuButton = document.querySelector("[data-mobile-menu-toggle]")
  const mobileMenu = document.querySelector("[data-mobile-menu]")

  if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden")
    })
  }

  // Initialize property image galleries
  const galleries = document.querySelectorAll(".property-gallery")
  galleries.forEach((gallery) => {
    const images = gallery.querySelectorAll("img")
    const thumbnails = gallery.querySelectorAll(".thumbnail")

    thumbnails.forEach((thumbnail, index) => {
      thumbnail.addEventListener("click", function () {
        // Hide all images
        images.forEach((img) => img.classList.add("hidden"))
        // Show selected image
        images[index].classList.remove("hidden")

        // Update thumbnail states
        thumbnails.forEach((thumb) => thumb.classList.remove("active"))
        this.classList.add("active")
      })
    })
  })

  // Initialize property filters
  const filterForm = document.querySelector("#property-filters")
  if (filterForm) {
    const inputs = filterForm.querySelectorAll("input, select")
    inputs.forEach((input) => {
      input.addEventListener("change", function () {
        // Auto-submit form when filters change
        if (this.type !== "text") {
          filterForm.submit()
        }
      })
    })
  }

  // Initialize booking form validation
  const bookingForm = document.querySelector("#booking-form")
  if (bookingForm) {
    bookingForm.addEventListener("submit", (e) => {
      const moveInDate = new Date(document.querySelector("#move_in_date").value)
      const moveOutDate = document.querySelector("#move_out_date").value

      if (moveOutDate) {
        const moveOutDateObj = new Date(moveOutDate)
        if (moveOutDateObj <= moveInDate) {
          e.preventDefault()
          alert("Move-out date must be after move-in date.")
          return false
        }
      }
    })
  }

  // Initialize message form
  const messageForm = document.querySelector("#message-form")
  if (messageForm) {
    const messageInput = messageForm.querySelector("textarea")
    const submitButton = messageForm.querySelector('button[type="submit"]')

    messageInput.addEventListener("input", function () {
      submitButton.disabled = this.value.trim().length === 0
    })
  }

  // Initialize verification file upload
  const fileInputs = document.querySelectorAll('input[type="file"]')
  fileInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const file = this.files[0]
      if (file) {
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
          alert("File size must be less than 5MB")
          this.value = ""
          return
        }

        // Validate file type for images
        if (this.accept && this.accept.includes("image/")) {
          const validTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"]
          if (!validTypes.includes(file.type)) {
            alert("Please select a valid image file (JPEG, PNG, GIF, WebP)")
            this.value = ""
            return
          }
        }
      }
    })
  })

  // Initialize property search autocomplete
  const searchInput = document.querySelector("#property-search")
  if (searchInput) {
    let searchTimeout

    searchInput.addEventListener("input", function () {
      clearTimeout(searchTimeout)
      const query = this.value.trim()

      if (query.length >= 3) {
        searchTimeout = setTimeout(() => {
          // Implement search suggestions here
          console.log("Searching for:", query)
        }, 300)
      }
    })
  }

  // Initialize property comparison
  const compareButtons = document.querySelectorAll(".compare-property")
  const compareList = JSON.parse(localStorage.getItem("compareProperties") || "[]")

  compareButtons.forEach((button) => {
    const propertyId = button.dataset.propertyId

    // Update button state based on comparison list
    if (compareList.includes(propertyId)) {
      button.classList.add("active")
      button.textContent = "Remove from Compare"
    }

    button.addEventListener("click", function () {
      const index = compareList.indexOf(propertyId)

      if (index > -1) {
        // Remove from comparison
        compareList.splice(index, 1)
        this.classList.remove("active")
        this.textContent = "Add to Compare"
      } else {
        // Add to comparison (max 3 properties)
        if (compareList.length >= 3) {
          alert("You can compare up to 3 properties at a time.")
          return
        }

        compareList.push(propertyId)
        this.classList.add("active")
        this.textContent = "Remove from Compare"
      }

      localStorage.setItem("compareProperties", JSON.stringify(compareList))
      updateCompareCounter()
    })
  })

  function updateCompareCounter() {
    const counter = document.querySelector("#compare-counter")
    if (counter) {
      counter.textContent = compareList.length
      counter.style.display = compareList.length > 0 ? "block" : "none"
    }
  }

  updateCompareCounter()

  // Initialize property favorites
  const favoriteButtons = document.querySelectorAll(".favorite-property")
  favoriteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const propertyId = this.dataset.propertyId

      // Toggle favorite state
      this.classList.toggle("active")

      // Send AJAX request to update favorite status
      fetch("/api/properties/" + propertyId + "/favorite", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Update UI based on response
            const icon = this.querySelector("svg")
            if (data.favorited) {
              icon.classList.add("text-red-500")
              icon.classList.remove("text-gray-400")
            } else {
              icon.classList.remove("text-red-500")
              icon.classList.add("text-gray-400")
            }
          }
        })
        .catch((error) => {
          console.error("Error updating favorite:", error)
          // Revert UI change on error
          this.classList.toggle("active")
        })
    })
  })

  // Initialize real-time notifications
  if (window.Echo) {
    window.Echo.private("App.Models.User." + window.userId).notification((notification) => {
      showNotification(notification)
    })
  }

  function showNotification(notification) {
    const notificationElement = document.createElement("div")
    notificationElement.className = "fixed top-4 right-4 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50 max-w-sm"
    notificationElement.innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold">${notification.title}</h4>
                    <p class="text-sm">${notification.message}</p>
                </div>
                <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `

    document.body.appendChild(notificationElement)

    // Auto-remove after 5 seconds
    setTimeout(() => {
      notificationElement.remove()
    }, 5000)
  }
})

// Global utility functions
window.formatCurrency = (amount, currency = "USD") =>
  new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: currency,
  }).format(amount)

window.formatDate = (date, options = {}) =>
  new Intl.DateTimeFormat("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    ...options,
  }).format(new Date(date))

window.debounce = (func, wait, immediate) => {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      timeout = null
      if (!immediate) func(...args)
    }
    const callNow = immediate && !timeout
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    if (callNow) func(...args)
  }
}
