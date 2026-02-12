// Corporate HR Management System JavaScript

// API Configuration
const API_BASE_URL = 'api';

// DOM Elements
const navItems = document.querySelectorAll('.nav-item');
const sections = document.querySelectorAll('.section');
const registerForm = document.getElementById('registerForm');
const searchBtn = document.getElementById('searchBtn');
const searchInput = document.getElementById('searchInput');
const updateForm = document.getElementById('updateForm');
const newStatusSelect = document.getElementById('newStatus');
const terminationDateGroup = document.getElementById('terminationDateGroup');
const pageTitle = document.getElementById('page-title');
const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
const sidebar = document.querySelector('.sidebar');
const mobileOverlay = document.querySelector('.mobile-overlay');

// Page titles mapping
const pageTitles = {
    dashboard: 'Dashboard',
    register: 'Add Employee',
    search: 'Employee Directory',
    update: 'Update Status'
};

// Initialize Application
document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
    initializeForms();
    initializeMobileMenu();
    loadDashboardStats();
    updateLastUpdateTime();
});

// Navigation System
function initializeNavigation() {
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const targetSection = this.dataset.section;
            switchSection(targetSection);
            
            // Update active nav item
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
            
            // Update page title
            pageTitle.textContent = pageTitles[targetSection] || 'HR System';
            
            // Close mobile menu if open
            closeMobileMenu();
        });
    });
    
    // Quick action buttons
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetSection = this.dataset.section;
            switchSection(targetSection);
            
            // Update active nav item
            navItems.forEach(nav => nav.classList.remove('active'));
            document.querySelector(`[data-section="${targetSection}"]`).classList.add('active');
            
            // Update page title
            pageTitle.textContent = pageTitles[targetSection] || 'HR System';
        });
    });
}

function switchSection(sectionId) {
    sections.forEach(section => {
        section.classList.remove('active');
    });
    
    const targetSection = document.getElementById(sectionId);
    if (targetSection) {
        targetSection.classList.add('active');
        
        // Load section-specific data
        if (sectionId === 'dashboard') {
            loadDashboardStats();
        }
    }
}

// Mobile Menu
function initializeMobileMenu() {
    mobileMenuBtn.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        mobileOverlay.classList.toggle('show');
    });
    
    mobileOverlay.addEventListener('click', closeMobileMenu);
}

function closeMobileMenu() {
    sidebar.classList.remove('open');
    mobileOverlay.classList.remove('show');
}

// Form Initialization
function initializeForms() {
    // Register Form
    registerForm.addEventListener('submit', handleEmployeeRegistration);
    
    // Search Functionality
    searchBtn.addEventListener('click', handleEmployeeSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            handleEmployeeSearch();
        }
    });
    
    // Update Form
    updateForm.addEventListener('submit', handleStatusUpdate);
    
    // Status Change Handler
    newStatusSelect.addEventListener('change', function() {
        if (this.value === 'terminated') {
            terminationDateGroup.style.display = 'block';
            document.getElementById('terminationDate').required = true;
        } else {
            terminationDateGroup.style.display = 'none';
            document.getElementById('terminationDate').required = false;
        }
    });
}

// Employee Registration
async function handleEmployeeRegistration(e) {
    e.preventDefault();
    
    const formData = new FormData(registerForm);
    const employeeData = {
        firstName: formData.get('firstName').trim(),
        lastName: formData.get('lastName').trim(),
        email: formData.get('email').trim(),
        position: formData.get('position').trim(),
        salary: parseFloat(formData.get('salary')),
        hireDate: formData.get('hireDate')
    };
    
    // Client-side validation
    if (!validateEmployeeData(employeeData)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/employees.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(employeeData)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            showToast('Employee registered successfully!', 'success');
            registerForm.reset();
            loadDashboardStats();
        } else {
            showToast(result.message || 'Registration failed', 'error');
        }
    } catch (error) {
        console.error('Registration error:', error);
        showToast('Network error occurred', 'error');
    }
}

// Employee Search
async function handleEmployeeSearch() {
    const searchTerm = searchInput.value.trim();
    
    if (!searchTerm) {
        showToast('Please enter search criteria', 'info');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/employees.php?search=${encodeURIComponent(searchTerm)}`);
        const result = await response.json();
        
        if (response.ok && result.success) {
            displaySearchResults(result.data);
        } else {
            showToast(result.message || 'Search failed', 'error');
            displaySearchResults([]);
        }
    } catch (error) {
        console.error('Search error:', error);
        showToast('Network error occurred', 'error');
        displaySearchResults([]);
    }
}

// Status Update
async function handleStatusUpdate(e) {
    e.preventDefault();
    
    const formData = new FormData(updateForm);
    const updateData = {
        employeeId: parseInt(formData.get('employeeId')),
        status: formData.get('status'),
        terminationDate: formData.get('terminationDate') || null
    };
    
    // Client-side validation
    if (!validateUpdateData(updateData)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/employees.php`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(updateData)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            showToast('Employee status updated successfully!', 'success');
            updateForm.reset();
            terminationDateGroup.style.display = 'none';
            loadDashboardStats();
        } else {
            showToast(result.message || 'Update failed', 'error');
        }
    } catch (error) {
        console.error('Update error:', error);
        showToast('Network error occurred', 'error');
    }
}

// Dashboard Statistics
async function loadDashboardStats() {
    try {
        const response = await fetch(`${API_BASE_URL}/employees.php?stats=true`);
        const result = await response.json();
        
        if (response.ok && result.success) {
            updateDashboardStats(result.data);
        }
    } catch (error) {
        console.error('Stats loading error:', error);
    }
}

function updateDashboardStats(stats) {
    document.getElementById('totalEmployees').textContent = stats.total || 0;
    document.getElementById('activeEmployees').textContent = stats.active || 0;
    document.getElementById('inactiveEmployees').textContent = stats.inactive || 0;
}

function updateLastUpdateTime() {
    const now = new Date();
    const timeString = now.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    document.getElementById('lastUpdate').textContent = timeString;
}

// Search Results Display
function displaySearchResults(employees) {
    const resultsContainer = document.getElementById('searchResults');
    
    if (!employees || employees.length === 0) {
        resultsContainer.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                <h3>No employees found</h3>
                <p>Try adjusting your search criteria</p>
            </div>
        `;
        return;
    }
    
    const resultsHTML = employees.map(employee => `
        <div class="employee-card">
            <div class="employee-header">
                <div class="employee-name">${employee.firstName} ${employee.lastName}</div>
                <div class="employee-status status-${employee.status}">${capitalizeFirst(employee.status)}</div>
            </div>
            <div class="employee-details">
                <div class="detail-item">
                    <div class="detail-label">Employee ID</div>
                    <div class="detail-value">#${employee.id}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">${employee.email}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Position</div>
                    <div class="detail-value">${employee.position}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Salary</div>
                    <div class="detail-value">$${parseFloat(employee.salary).toLocaleString()}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Hire Date</div>
                    <div class="detail-value">${formatDate(employee.hireDate)}</div>
                </div>
                ${employee.terminationDate ? `
                <div class="detail-item">
                    <div class="detail-label">Termination Date</div>
                    <div class="detail-value">${formatDate(employee.terminationDate)}</div>
                </div>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    resultsContainer.innerHTML = resultsHTML;
}

// Validation Functions
function validateEmployeeData(data) {
    const errors = [];
    
    // Required fields validation
    if (!data.firstName) errors.push('First name is required');
    if (!data.lastName) errors.push('Last name is required');
    if (!data.email) errors.push('Email is required');
    if (!data.position) errors.push('Position is required');
    if (!data.salary) errors.push('Salary is required');
    if (!data.hireDate) errors.push('Hire date is required');
    
    // Email format validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (data.email && !emailRegex.test(data.email)) {
        errors.push('Invalid email format');
    }
    
    // Salary validation
    if (data.salary && (isNaN(data.salary) || data.salary < 0)) {
        errors.push('Salary must be a valid positive number');
    }
    
    // Name validation (no numbers)
    const nameRegex = /^[a-zA-Z\s]+$/;
    if (data.firstName && !nameRegex.test(data.firstName)) {
        errors.push('First name should contain only letters');
    }
    if (data.lastName && !nameRegex.test(data.lastName)) {
        errors.push('Last name should contain only letters');
    }
    
    // Date validation
    if (data.hireDate && new Date(data.hireDate) > new Date()) {
        errors.push('Hire date cannot be in the future');
    }
    
    if (errors.length > 0) {
        showToast(errors.join(', '), 'error');
        return false;
    }
    
    return true;
}

function validateUpdateData(data) {
    const errors = [];
    
    // Required fields validation
    if (!data.employeeId || isNaN(data.employeeId) || data.employeeId <= 0) {
        errors.push('Valid employee ID is required');
    }
    
    if (!data.status) {
        errors.push('Status is required');
    }
    
    // Valid status values
    const validStatuses = ['active', 'inactive', 'terminated'];
    if (data.status && !validStatuses.includes(data.status)) {
        errors.push('Invalid status value');
    }
    
    // Termination date validation
    if (data.status === 'terminated' && !data.terminationDate) {
        errors.push('Termination date is required when status is terminated');
    }
    
    if (data.terminationDate && new Date(data.terminationDate) > new Date()) {
        errors.push('Termination date cannot be in the future');
    }
    
    if (errors.length > 0) {
        showToast(errors.join(', '), 'error');
        return false;
    }
    
    return true;
}

// Utility Functions
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type}`;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 4000);
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}
