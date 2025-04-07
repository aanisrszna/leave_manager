<script>
        // Function to generate DOB based on IC Number
        function generateDOB() {
            let icNumber = document.querySelector('input[name="ic_number"]').value;
            if (/^\d{6}-\d{2}-\d{4}$/.test(icNumber)) {
                let year = parseInt(icNumber.substring(0, 2), 10);
                let month = icNumber.substring(2, 4);
                let day = icNumber.substring(4, 6);
                const currentYear = new Date().getFullYear();
                const currentYearPrefix = currentYear.toString().substring(0, 2);

                // If the year is less than 10, use '200' instead of '20'
                if (year < 10) {
                    year = '200' + year;
                } else {
                    year = year > parseInt(currentYearPrefix, 10) ? '19' + year : '20' + year;
                }

                document.getElementById('dob').value = `${year}-${month}-${day}`;
            }
        }
        // Function to calculate Service Year based on Date Joined
        function generateServiceYear() {
            let dateJoined = document.querySelector('input[name="date_joined"]').value;
            if (dateJoined) {
                let currentYear = new Date().getFullYear();
                let joinedYear = new Date(dateJoined).getFullYear();
                let serviceYear = currentYear - joinedYear;
                document.getElementById('service_year').value = serviceYear;
            }
        }
    </script>
