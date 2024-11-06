# Customer Management API

This is a Laravel-based RESTful API for managing customer data. The application allows creating, reading, updating, and deleting (CRUD) customer records, along with filtering options by name and email.

## Table of Contents
- [Building and Running the Application](#building-and-running-the-application)
- [API Endpoints and Sample Requests](#api-endpoints-and-sample-requests)
- [Accessing the SQLite Database Console](#accessing-the-sqlite-database-console)
- [Assumptions Made](#assumptions-made)

---

### Building and Running the Application

1. **Install Docker Desktop and Docker Compose**:
   - **Docker Desktop**: Download and install Docker Desktop based on your operating system:
     - [Docker Desktop for Windows](https://docs.docker.com/desktop/install/windows-install/)
     - [Docker Desktop for Mac](https://docs.docker.com/desktop/install/mac-install/)
     - [Docker Desktop for Linux](https://docs.docker.com/desktop/install/linux-install/)
   - **Docker Compose**: Install Docker Compose separately, as it is required for running the application scripts:
     - [Docker Compose Installation Guide](https://docs.docker.com/compose/install/)

2. **Clone the Repository**:
   ```bash
   git clone https://github.com/theadawifamily/CustomerManagementAPI.git

3. **Build and Start the Containers**:
   Run the following command to build and start the application with Docker Compose:
   ```bash
   docker-compose up -d --build

