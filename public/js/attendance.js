/**
 * Attendance Management JavaScript
 * Handles real-time attendance tracking, break management, and UI updates
 */

class AttendanceTracker {
    constructor() {
        this.isOnBreak = false;
        this.breakStartTime = null;
        this.currentTime = new Date();
        this.workingTimer = null;
        this.breakTimer = null;
        this.autoRefreshInterval = null;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.startRealTimeClock();
        this.setupAutoRefresh();
        this.initializeBreakStatus();
        this.setupGeolocation();
    }

    setupEventListeners() {
        // Check-in button
        const checkInBtn = document.getElementById('check-in-btn');
        if (checkInBtn) {
            checkInBtn.addEventListener('click', () => this.checkIn());
        }

        // Check-out button
        const checkOutBtn = document.getElementById('check-out-btn');
        if (checkOutBtn) {
            checkOutBtn.addEventListener('click', () => this.checkOut());
        }

        // Break toggle button
        const breakBtn = document.getElementById('break-btn');
        if (breakBtn) {
            breakBtn.addEventListener('click', () => this.toggleBreak());
        }

        // Window visibility change for accurate timing
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.syncTimeWithServer();
            }
        });
    }

    startRealTimeClock() {
        const updateClock = () => {
            this.currentTime = new Date();
            this.updateTimeDisplay();
            this.updateWorkingHours();
        };

        updateClock();
        setInterval(updateClock, 1000);
    }

    updateTimeDisplay() {
        const timeElements = document.querySelectorAll('.current-time');
        const timeString = this.currentTime.toLocaleTimeString('en-US', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        timeElements.forEach(element => {
            element.textContent = timeString;
        });

        // Update main time display
        const mainTimeDisplay = document.querySelector('.attendance-time-display h4');
        if (mainTimeDisplay) {
            mainTimeDisplay.textContent = timeString;
        }
    }

    updateWorkingHours() {
        const checkInTime = document.getElementById('check-in-time')?.textContent;
        const checkOutTime = document.getElementById('check-out-time')?.textContent;
        
        if (checkInTime && checkInTime !== '--:--' && (!checkOutTime || checkOutTime === '--:--')) {
            // Currently working - calculate live working hours
            const checkIn = this.parseTimeString(checkInTime);
            if (checkIn) {
                const workingMinutes = this.calculateMinutesDifference(checkIn, this.currentTime);
                const breakDuration = this.getCurrentBreakDuration();
                const netWorkingMinutes = Math.max(0, workingMinutes - breakDuration);
                
                this.updateWorkingHoursDisplay(netWorkingMinutes);
            }
        }
    }

    parseTimeString(timeString) {
        const [hours, minutes, seconds] = timeString.split(':').map(Number);
        const date = new Date();
        date.setHours(hours, minutes, seconds || 0, 0);
        return date;
    }

    calculateMinutesDifference(startTime, endTime) {
        return Math.floor((endTime - startTime) / (1000 * 60));
    }

    getCurrentBreakDuration() {
        // This would typically fetch from the server or local storage
        const breakTimeElement = document.getElementById('break-time');
        if (breakTimeElement) {
            const breakTime = breakTimeElement.textContent;
            const [hours, minutes] = breakTime.split(':').map(Number);
            return (hours * 60) + minutes;
        }
        return 0;
    }

    updateWorkingHoursDisplay(totalMinutes) {
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        
        const workingHoursElement = document.getElementById('working-hours');
        if (workingHoursElement) {
            workingHoursElement.textContent = formattedTime;
        }
    }

    async checkIn() {
        try {
            this.showLoading('Checking in...');
            
            const response = await this.makeRequest('/check-in', 'POST', {
                location: this.getCurrentLocation(),
                timestamp: new Date().toISOString()
            });

            if (response.success) {
                this.handleCheckInSuccess(response);
                this.showNotification('success', response.message);
            } else {
                this.showNotification('error', response.message);
            }
        } catch (error) {
            console.error('Check-in error:', error);
            this.showNotification('error', 'Failed to check in. Please try again.');
        } finally {
            this.hideLoading();
        }
    }

    async checkOut() {
        try {
            // Confirm check-out
            const confirmResult = await this.showConfirmation(
                'Confirm Check-out',
                'Are you sure you want to check out now?',
                'warning'
            );

            if (!confirmResult.isConfirmed) return;

            this.showLoading('Checking out...');
            
            const response = await this.makeRequest('/check-out', 'POST', {
                location: this.getCurrentLocation(),
                timestamp: new Date().toISOString()
            });

            if (response.success) {
                this.handleCheckOutSuccess(response);
                this.showNotification('success', response.message);
            } else {
                this.showNotification('error', response.message);
            }
        } catch (error) {
            console.error('Check-out error:', error);
            this.showNotification('error', 'Failed to check out. Please try again.');
        } finally {
            this.hideLoading();
        }
    }

    async toggleBreak() {
        try {
            const action = this.isOnBreak ? 'end-break' : 'start-break';
            const message = this.isOnBreak ? 'Ending break...' : 'Starting break...';
            
            this.showLoading(message);
            
            const response = await this.makeRequest(`/${action}`, 'POST', {
                timestamp: new Date().toISOString()
            });

            if (response.success) {
                this.isOnBreak = !this.isOnBreak;
                this.updateBreakButton();
                this.showNotification('success', response.message);
                
                if (response.total_break_duration) {
                    this.updateBreakDuration(response.total_break_duration);
                }
            } else {
                this.showNotification('error', response.message);
            }
        } catch (error) {
            console.error('Break toggle error:', error);
            this.showNotification('error', 'Failed to manage break. Please try again.');
        } finally {
            this.hideLoading();
        }
    }

    handleCheckInSuccess(response) {
        // Update UI elements
        document.getElementById('check-in-time').textContent = 
            new Date().toLocaleTimeString('en-US', { hour12: false });
        
        document.getElementById('check-in-btn').disabled = true;
        document.getElementById('check-out-btn').disabled = false;
        
        const breakBtn = document.getElementById('break-btn');
        if (breakBtn) breakBtn.disabled = false;
        
        // Update status badge
        this.updateStatusBadge('present', 'Present');
        
        // Start working timer
        this.startWorkingTimer();
    }

    handleCheckOutSuccess(response) {
        // Update UI elements
        document.getElementById('check-out-time').textContent = 
            new Date().toLocaleTimeString('en-US', { hour12: false });
        
        document.getElementById('check-out-btn').disabled = true;
        document.getElementById('break-btn').disabled = true;
        
        // Update working hours if provided
        if (response.attendance && response.attendance.formatted_total_hours) {
            document.getElementById('working-hours').textContent = 
                response.attendance.formatted_total_hours;
        }
        
        // Stop working timer
        this.stopWorkingTimer();
        
        // Check for overtime
        if (response.attendance && response.attendance.overtime_hours > 0) {
            this.showOvertimeNotification(response.attendance.overtime_hours);
        }
    }

    updateBreakButton() {
        const breakBtn = document.getElementById('break-btn');
        const breakText = document.getElementById('break-text');
        
        if (!breakBtn || !breakText) return;
        
        if (this.isOnBreak) {
            breakBtn.className = 'btn btn-success';
            breakText.textContent = 'End Break';
            breakBtn.innerHTML = '<i class="fa fa-play me-2"></i>End Break';
        } else {
            breakBtn.className = 'btn btn-warning';
            breakText.textContent = 'Start Break';
            breakBtn.innerHTML = '<i class="fa fa-pause me-2"></i>Start Break';
        }
    }

    updateStatusBadge(status, label) {
        const statusBadge = document.getElementById('attendance-status');
        if (!statusBadge) return;
        
        const statusClasses = {
            'present': 'bg-success',
            'late': 'bg-warning',
            'half_day': 'bg-info',
            'early_leave': 'bg-warning',
            'absent': 'bg-danger',
            'unpaid_leave': 'bg-secondary',
            'without_notice': 'bg-danger'
        };
        
        statusBadge.className = `badge ${statusClasses[status] || 'bg-secondary'}`;
        statusBadge.textContent = label;
    }

    updateBreakDuration(duration) {
        const breakTimeElement = document.getElementById('break-time');
        if (breakTimeElement) {
            breakTimeElement.textContent = duration;
        }
    }

    initializeBreakStatus() {
        // Check if user is currently on break from server data
        const breakTimes = window.attendanceData?.break_times || [];
        
        breakTimes.forEach(breakTime => {
            if (breakTime.start && !breakTime.end) {
                this.isOnBreak = true;
                this.breakStartTime = breakTime.start;
                this.updateBreakButton();
            }
        });
    }

    setupGeolocation() {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                },
                (error) => {
                    console.warn('Geolocation not available:', error);
                    this.currentLocation = null;
                }
            );
        }
    }

    getCurrentLocation() {
        return this.currentLocation || null;
    }

    startWorkingTimer() {
        if (this.workingTimer) clearInterval(this.workingTimer);
        
        this.workingTimer = setInterval(() => {
            this.updateWorkingHours();
        }, 60000); // Update every minute
    }

    stopWorkingTimer() {
        if (this.workingTimer) {
            clearInterval(this.workingTimer);
            this.workingTimer = null;
        }
    }

    setupAutoRefresh() {
        // Refresh data every 5 minutes to sync with server
        this.autoRefreshInterval = setInterval(() => {
            this.syncWithServer();
        }, 300000); // 5 minutes
    }

    async syncWithServer() {
        try {
            const response = await this.makeRequest('/stats', 'GET');
            if (response) {
                this.updateUIWithServerData(response);
            }
        } catch (error) {
            console.warn('Failed to sync with server:', error);
        }
    }

    async syncTimeWithServer() {
        try {
            const response = await fetch('/api/server-time');
            const data = await response.json();
            this.currentTime = new Date(data.timestamp);
        } catch (error) {
            console.warn('Failed to sync time with server:', error);
        }
    }

    updateUIWithServerData(data) {
        // Update statistics and other UI elements with fresh server data
        Object.keys(data).forEach(key => {
            const element = document.getElementById(key.replace(/_/g, '-'));
            if (element) {
                element.textContent = data[key];
            }
        });
    }

    async makeRequest(endpoint, method = 'GET', data = null) {
        const baseUrl = window.location.pathname.includes('/employee/') 
            ? '/employee/attendance' 
            : `/${window.userRole}/attendance`;
        
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        };

        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(baseUrl + endpoint, options);
        return await response.json();
    }

    showLoading(message = 'Processing...') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    }

    hideLoading() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }

    showNotification(type, message, timer = 3000) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Success!' : 'Error!',
                text: message,
                timer: timer,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // Fallback to browser notification
            alert(message);
        }
    }

    async showConfirmation(title, text, icon = 'question') {
        if (typeof Swal !== 'undefined') {
            return await Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            });
        } else {
            return { isConfirmed: confirm(text) };
        }
    }

    showOvertimeNotification(overtimeHours) {
        this.showNotification('info', 
            `Great work! You've completed ${overtimeHours} hours of overtime today.`, 
            5000
        );
    }

    // Utility method to format time
    formatTime(minutes) {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
    }

    // Clean up timers when page unloads
    destroy() {
        if (this.workingTimer) clearInterval(this.workingTimer);
        if (this.breakTimer) clearInterval(this.breakTimer);
        if (this.autoRefreshInterval) clearInterval(this.autoRefreshInterval);
    }
}

// Attendance Management Functions for HR/Admin
class AttendanceManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupDataTables();
        this.setupFilters();
    }

    setupEventListeners() {
        // Mark attendance form
        const markAttendanceForm = document.getElementById('markAttendanceForm');
        if (markAttendanceForm) {
            markAttendanceForm.addEventListener('submit', (e) => this.handleMarkAttendance(e));
        }

        // Filter form
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => this.handleFilterSubmit(e));
        }

        // Export button
        const exportBtn = document.querySelector('[onclick="exportAttendance()"]');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => this.exportAttendance());
        }
    }

    setupDataTables() {
        const table = document.getElementById('attendanceTable');
        if (table && typeof $ !== 'undefined' && $.fn.DataTable) {
            $(table).DataTable({
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']], // Sort by date descending
                columnDefs: [
                    { orderable: false, targets: [-1] } // Disable sorting on actions column
                ]
            });
        }
    }

    setupFilters() {
        // Date range validation
        const dateFromInput = document.querySelector('input[name="date_from"]');
        const dateToInput = document.querySelector('input[name="date_to"]');

        if (dateFromInput && dateToInput) {
            dateFromInput.addEventListener('change', () => {
                dateToInput.min = dateFromInput.value;
            });

            dateToInput.addEventListener('change', () => {
                dateFromInput.max = dateToInput.value;
            });
        }
    }

    async handleMarkAttendance(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await this.makeRequest('/mark', 'POST', data);
            
            if (response.success) {
                this.showNotification('success', response.message);
                event.target.reset();
                document.querySelector('[data-bs-dismiss="modal"]').click();
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showNotification('error', response.message);
            }
        } catch (error) {
            console.error('Mark attendance error:', error);
            this.showNotification('error', 'Failed to mark attendance');
        }
    }

    async exportAttendance() {
        try {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = `/attendance/export?${params.toString()}`;
            
            this.showNotification('info', 'Preparing export...', 2000);
            window.location.href = exportUrl;
        } catch (error) {
            console.error('Export error:', error);
            this.showNotification('error', 'Failed to export data');
        }
    }

    async makeRequest(endpoint, method = 'GET', data = null) {
        const baseUrl = `/${window.userRole}/attendance`;
        
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        };

        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(baseUrl + endpoint, options);
        return await response.json();
    }

    showNotification(type, message, timer = 3000) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Success!' : (type === 'error' ? 'Error!' : 'Info'),
                text: message,
                timer: timer,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    }
}

// Initialize appropriate class based on page
document.addEventListener('DOMContentLoaded', function() {
    // Set user role for API requests
    const userRoles = ['Employee', 'HR', 'Admin', 'superAdmin'];
    const currentPath = window.location.pathname;
    
    for (const role of userRoles) {
        if (currentPath.includes(`/${role.toLowerCase()}/`)) {
            window.userRole = role.toLowerCase();
            break;
        }
    }

    // Initialize appropriate attendance handler
    if (window.userRole === 'employee') {
        window.attendanceTracker = new AttendanceTracker();
    } else {
        window.attendanceManager = new AttendanceManager();
    }

    // Handle page unload
    window.addEventListener('beforeunload', function() {
        if (window.attendanceTracker) {
            window.attendanceTracker.destroy();
        }
    });
});

// Global functions for backward compatibility
function checkIn() {
    if (window.attendanceTracker) {
        window.attendanceTracker.checkIn();
    }
}

function checkOut() {
    if (window.attendanceTracker) {
        window.attendanceTracker.checkOut();
    }
}

function toggleBreak() {
    if (window.attendanceTracker) {
        window.attendanceTracker.toggleBreak();
    }
}

function exportAttendance() {
    if (window.attendanceManager) {
        window.attendanceManager.exportAttendance();
    }
}

