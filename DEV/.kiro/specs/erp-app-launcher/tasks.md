# Implementation Plan

- [x] 1. Set up project structure and database schema



  - Create directory structure for launcher module (ctrl, mdl, js folders)
  - Create database tables: erp_modules, erp_module_permissions, erp_access_logs
  - Insert initial module data (KPI, Producción, Contabilidad, CostSys)




  - _Requirements: 1.1, 4.1, 4.2_

- [x] 2. Implement backend model (mdl-launcher.php)

- [ ] 2.1 Create base model class structure
  - Extend CRUD class and configure database connection
  - Declare $bd and $util properties
  - _Requirements: 4.1, 4.2_


- [ ] 2.2 Implement module data access methods
  - Create listModules() method to retrieve all active modules
  - Create getModulesByUser() method with permission filtering
  - Create getModuleById() method for single module retrieval
  - _Requirements: 1.1, 4.2_

- [ ] 2.3 Implement access logging methods
  - Create createAccessLog() method to record module access
  - Create listAccessLogsByUser() method for audit queries



  - _Requirements: 4.5_

- [ ]* 2.4 Write unit tests for model methods
  - Test listModules() returns correct data structure

  - Test getModulesByUser() filters by permissions correctly
  - Test createAccessLog() inserts records properly
  - _Requirements: 4.1, 4.2, 4.5_


- [ ] 3. Implement backend controller (ctrl-launcher.php)
- [ ] 3.1 Create controller class structure
  - Extend mdl class and configure session handling
  - Implement request validation for $_POST['opc']
  - _Requirements: 4.1_


- [ ] 3.2 Implement init() method
  - Return initial configuration data
  - Include system status and version info
  - _Requirements: 5.1, 5.2, 5.3_

- [ ] 3.3 Implement getModules() method
  - Call getModulesByUser() with current user session
  - Format module data for frontend consumption
  - Handle empty results gracefully



  - _Requirements: 1.1, 4.2, 4.4_

- [ ] 3.4 Implement logAccess() method
  - Validate module access permissions
  - Record access attempt in database

  - Return success/error response
  - _Requirements: 4.5_

- [ ]* 3.5 Write unit tests for controller methods
  - Test init() returns correct structure
  - Test getModules() filters by user permissions
  - Test logAccess() validates and records correctly

  - _Requirements: 4.1, 4.2, 4.5_

- [ ] 4. Implement frontend JavaScript (launcher.js)
- [ ] 4.1 Create App class structure
  - Extend Templates class from CoffeeSoft
  - Define PROJECT_NAME and initialize properties

  - Implement constructor with api link and root div
  - _Requirements: 1.1_

- [ ] 4.2 Implement layout() method
  - Create main container structure using primaryLayout()
  - Add header with logo and system title
  - Create search bar container

  - Create module cards grid container
  - Create footer with system status
  - _Requirements: 1.2, 1.3, 5.1_

- [ ] 4.3 Implement loadModules() method
  - Call backend API using useFetch() with opc: "getModules"
  - Store modules in class property

  - Handle loading states and errors
  - Call renderModuleCards() with retrieved data
  - _Requirements: 1.1, 4.2_

- [ ] 4.4 Implement renderModuleCards() method
  - Iterate through modules array

  - Create card HTML structure for each module
  - Apply icon, title, description, and status badge
  - Attach click event to navigate to module URL
  - Apply hover effects and transitions
  - _Requirements: 1.2, 2.1, 2.2, 2.3_

- [ ] 4.5 Implement filterModules() method
  - Get search term from input field
  - Filter modules array by name and description
  - Update module count display
  - Re-render filtered module cards

  - Show "no results" message if needed

  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [ ] 4.6 Implement navigateToModule() method
  - Validate module URL before navigation
  - Log access attempt via backend API

  - Redirect to module URL on success
  - Handle navigation errors
  - _Requirements: 2.1, 4.5_

- [x] 4.7 Implement search bar functionality

  - Attach keyup event listener to search input
  - Apply debounce (300ms) to search function
  - Call filterModules() on input change
  - _Requirements: 3.1, 3.2, 3.5_

- [x]* 4.8 Write unit tests for frontend methods

  - Test loadModules() fetches and stores data correctly
  - Test filterModules() filters by search term
  - Test renderModuleCards() creates correct HTML

  - Test navigateToModule() validates and redirects

  - _Requirements: 1.1, 2.1, 3.1_

- [ ] 5. Create entry point HTML (index.php)
- [ ] 5.1 Create basic HTML structure
  - Add DOCTYPE and HTML5 boilerplate

  - Include meta tags for charset and viewport
  - Add page title "Lanzador de Aplicaciones - ERP"
  - _Requirements: 1.1_


- [ ] 5.2 Include CSS dependencies
  - Link TailwindCSS CDN or compiled CSS
  - Link Font Awesome for icons

  - Link custom CoffeeSoft styles

  - _Requirements: 1.2_

- [ ] 5.3 Include JavaScript dependencies
  - Load jQuery library

  - Load CoffeeSoft framework (coffeeSoft.js)
  - Load plugins.js for utilities
  - Load launcher.js as main application script
  - _Requirements: 1.1_


- [ ] 5.4 Create root container
  - Add <div id="root"></div> as main mount point
  - Initialize App instance on document ready

  - _Requirements: 1.1_

- [x] 6. Implement responsive design

- [x] 6.1 Configure grid layout breakpoints

  - Desktop: 4 columns (1920px+)
  - Tablet: 2-3 columns (768px-1919px)
  - Mobile: 1 column (< 768px)
  - _Requirements: 1.2_


- [ ] 6.2 Adjust card sizing for different screens
  - Set min-height and max-width constraints
  - Adjust padding and margins for mobile

  - _Requirements: 1.2_

- [ ] 6.3 Optimize search bar for mobile
  - Full width on mobile devices

  - Adjust font size and padding
  - _Requirements: 3.1_


- [x] 7. Implement security features

- [ ] 7.1 Add session validation
  - Check user session before loading modules
  - Redirect to login if session is invalid
  - _Requirements: 4.1_


- [ ] 7.2 Implement permission checking
  - Validate user permissions on backend
  - Filter modules based on user role

  - _Requirements: 4.2_


- [ ] 7.3 Add input sanitization
  - Sanitize search input to prevent XSS
  - Escape HTML output in module descriptions

  - _Requirements: 3.1_

- [ ] 7.4 Implement CSRF protection
  - Add CSRF token to API requests

  - Validate token on backend
  - _Requirements: 4.1_



- [ ] 8. Add error handling and user feedback
- [ ] 8.1 Implement loading states
  - Show spinner while loading modules
  - Disable interactions during loading
  - _Requirements: 1.1_

- [ ] 8.2 Add error messages
  - Display user-friendly error messages
  - Provide retry options for failed requests
  - _Requirements: 4.4_

- [ ] 8.3 Implement empty state
  - Show message when no modules are available
  - Provide contact information for support
  - _Requirements: 4.4_

- [ ] 8.4 Add search no-results state
  - Display message when search returns no results
  - Suggest clearing search or trying different terms
  - _Requirements: 3.3_

- [ ] 9. Implement system status indicator
- [ ] 9.1 Create status component
  - Display online/offline indicator
  - Show system version number
  - Display module count
  - _Requirements: 5.1, 5.2, 5.3_

- [ ] 9.2 Add real-time status updates
  - Poll backend for system status
  - Update indicator color based on status
  - _Requirements: 5.4_

- [ ] 10. Optimize performance
- [ ] 10.1 Implement module caching
  - Cache module list in session storage
  - Refresh cache on user action or timeout
  - _Requirements: 1.1_

- [ ] 10.2 Add search debouncing
  - Debounce search input (300ms delay)
  - Cancel previous search requests
  - _Requirements: 3.1_

- [ ] 10.3 Optimize image and icon loading
  - Use icon fonts instead of images where possible
  - Lazy load module icons
  - _Requirements: 1.2_

- [ ] 11. Wire everything together and test end-to-end
  - Verify complete user flow: login → launcher → module selection → navigation
  - Test search functionality across all modules
  - Verify permission-based filtering works correctly
  - Test responsive design on multiple devices
  - Validate error handling for edge cases
  - _Requirements: 1.1, 1.2, 1.3, 2.1, 3.1, 4.2, 5.1_
