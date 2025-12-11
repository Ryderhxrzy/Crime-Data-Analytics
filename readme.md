/MAIN FOLDER
  ├── /api                   # Lahat ng backend files, API endpoints, config, etc. dito ilalagay
  │     ├── /action
  │     ├── /retrieve
  │     ├── config.php       # Huwag babaguhin ang config.php, dito naka-handle ang DB connection
  │     ├── test.php         # Para sa testing ng connection o API
  ├── /frontend              # Frontend application files dito ilalagay
  │     └── index.php
  ├── .env                   # Database credentials or other sensitive information dito lang ilalagay, huwag i-commit sa git
  ├── .env.example           # Template ng .env para malaman niyo anong laman ng config
  ├── .gitignore             
  ├── db.sql                 # Import yung db.sql para pare-parehas tayo ng name ng Database



