# Corporate HR Management System 🏢

A professional, responsive employee management system built with HTML, CSS, JavaScript, and PHP. Features a modern corporate design with comprehensive employee management capabilities.

## Features ✨

- **Employee Registration**: Add new employees with complete validation
- **Employee Directory**: Search and view employee information
- **Status Management**: Update employee status (active, inactive, terminated)
- **Dashboard Analytics**: View real-time employee statistics
- **Responsive Design**: Optimized for desktop, tablet, and mobile devices
- **Professional UI**: Clean, corporate interface with intuitive navigation

## Technical Requirements Met 📋

### Functional Requirements
- ✅ Register employees
- ✅ Query employee information
- ✅ Update employee status
- ✅ Register contract termination

### Acceptance Criteria
1. ✅ Valid data processing with proper operation handling
2. ✅ Business rule validation (email format, salary ranges, date validation)
3. ✅ Clear user feedback (success confirmations, error messages)
4. ✅ Complete data validation:
   - Required fields validation
   - Numeric value validation
   - Non-numeric value validation
   - Allowed ranges validation
5. ✅ Source code written entirely in English
6. ✅ Consistent naming convention (camelCase)
7. ✅ Ready for remote repository

### Additional Features
- ✅ Implemented as REST API
- ✅ Clear endpoints (GET, POST, PUT)
- ✅ Fully responsive design for all devices
- ✅ Professional corporate interface

## Installation & Setup 🚀

### Prerequisites
- XAMPP or WAMP server
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Setup Instructions

1. **Clone or download the project**
   ```bash
   git clone [repository-url]
   cd corporate-hr-system
   ```

2. **Setup Database**
   - Open phpMyAdmin
   - Import the `database_setup.sql` file
   - Or manually create the database using the SQL script

3. **Configure Database Connection**
   - Edit `api/database.php` if needed
   - Default settings:
     - Host: localhost
     - Database: employee_management
     - Username: root
     - Password: (empty)

4. **Start Server**
   - Place project in your web server directory (htdocs for XAMPP)
   - Start Apache and MySQL services
   - Access via: `http://localhost/corporate-hr-system`

## API Endpoints 🔗

### GET Requests
- `GET /api/employees.php?stats=true` - Get employee statistics
- `GET /api/employees.php?id={id}` - Get employee by ID
- `GET /api/employees.php?search={term}` - Search employees

### POST Requests
- `POST /api/employees.php` - Register new employee
  ```json
  {
    "firstName": "John",
    "lastName": "Doe",
    "email": "john.doe@company.com",
    "position": "Software Developer",
    "salary": 75000.00,
    "hireDate": "2024-01-15"
  }
  ```

### PUT Requests
- `PUT /api/employees.php` - Update employee status
  ```json
  {
    "employeeId": 1,
    "status": "terminated",
    "terminationDate": "2024-12-31"
  }
  ```

## Validation Rules 📝

### Employee Registration
- **First Name**: Required, letters only
- **Last Name**: Required, letters only
- **Email**: Required, valid email format, unique
- **Position**: Required, any text
- **Salary**: Required, positive number
- **Hire Date**: Required, cannot be future date

### Status Update
- **Employee ID**: Required, valid numeric ID
- **Status**: Required, must be 'active', 'inactive', or 'terminated'
- **Termination Date**: Required when status is 'terminated', cannot be future date

## Responsive Design 📱

The system is fully responsive and optimized for:

### Desktop (1024px+)
- Full sidebar navigation
- Multi-column layouts
- Comprehensive dashboard view
- Side-by-side form fields

### Tablet (768px - 1024px)
- Collapsible sidebar
- Adjusted grid layouts
- Touch-friendly interface
- Optimized spacing

### Mobile (< 768px)
- Hidden sidebar with overlay menu
- Single-column layouts
- Stack form fields
- Touch-optimized buttons
- Simplified navigation

### Key Responsive Features
- Flexible CSS Grid and Flexbox layouts
- Mobile-first approach
- Touch-friendly interface elements
- Optimized typography scaling
- Adaptive navigation patterns
- Responsive tables and cards

## Design System 🎨

### Color Palette
- **Primary**: #3b82f6 (Blue)
- **Success**: #10b981 (Green)
- **Error**: #ef4444 (Red)
- **Background**: #f8fafc (Light Gray)
- **Text**: #1e293b (Dark Gray)

### Typography
- **Font Family**: Inter (Professional, readable)
- **Weights**: 300, 400, 500, 600, 700
- **Responsive scaling**: Adapts to screen size

### Components
- Modern card-based layouts
- Consistent spacing system
- Professional form styling
- Subtle shadows and borders
- Smooth transitions and animations

## Browser Support 🌐

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## File Structure 📁

```
corporate-hr-system/
├── index.html              # Main application page
├── styles.css              # Responsive CSS styles
├── script.js               # Frontend JavaScript
├── database_setup.sql      # Database creation script
├── README.md              # Documentation
└── api/
    ├── database.php       # Database connection class
    ├── Employee.php       # Employee management class
    └── employees.php      # REST API endpoints
```

## Performance Features ⚡

- Optimized CSS with minimal redundancy
- Efficient JavaScript with modern ES6+ features
- Lazy loading of dashboard statistics
- Minimal HTTP requests
- Compressed and optimized assets

## Security Features 🔒

- SQL injection prevention with prepared statements
- Input validation on both client and server side
- CORS headers for API security
- XSS prevention through proper data handling
- Secure password handling (if authentication added)

## Contributing 🤝

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Make your changes
4. Test thoroughly on multiple devices
5. Commit your changes (`git commit -am 'Add new feature'`)
6. Push to the branch (`git push origin feature/new-feature`)
7. Submit a pull request

## License 📄

This project is open source and available under the MIT License.

---

**Corporate HR Management System** - Professional employee management made simple.