document.addEventListener("DOMContentLoaded", function () {
    // Fetch districts on page load
    fetch("get_districts.php")
        .then(response => response.json())
        .then(data => {
            const districtSelect = document.getElementById("district");

            // Populate the district dropdown
            data.forEach(district => {
                const option = document.createElement("option");
                option.value = district.district_name; // Assuming district_name is the identifier
                option.textContent = district.district_name;
                districtSelect.appendChild(option);
            });

            // Show the institution form when a district is selected
            districtSelect.addEventListener("change", function () {
                if (this.value !== "") {
                    fetchInstitutions(this.value);
                }
            });
        })
        .catch(error => console.error("Error fetching districts:", error));
});

// Fetch institutions based on district
function fetchInstitutions(districtName) {
    fetch(`get_institutions.php?district=${districtName}`)
        .then(response => response.json())
        .then(data => {
            const institutionForm = document.getElementById("institution-form");
            const institutionSelect = document.getElementById("institution");

            // Clear existing options and add new ones
            institutionSelect.innerHTML = '<option value="">--Select Institution--</option>';
            data.forEach(institution => {
                const option = document.createElement("option");
                option.value = institution.institution_id; // Assuming institution_id is returned
                option.textContent = institution.institution_name;
                institutionSelect.appendChild(option);
            });

            institutionForm.style.display = "block";

            // Show batch form when an institution is selected
            institutionSelect.addEventListener("change", function () {
                if (this.value !== "") {
                    document.getElementById("batch-form").style.display = "block";
                }
            });
        })
        .catch(error => console.error("Error fetching institutions:", error));
}

// Handle batch search
document.getElementById("batch-submit").addEventListener("click", function () {
    const institutionId = document.getElementById("institution").value;
    const batchYear = document.getElementById("batch").value;

    if (institutionId && batchYear) {
        fetch(`get_students.php?institution_id=${institutionId}&batch_year=${batchYear}`)
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById("batch-results");
                resultsDiv.style.display = "block";
                resultsDiv.innerHTML = "<h3>Batch Results:</h3>";

                if (data.length > 0) {
                    data.forEach(student => {
                        const studentInfo = document.createElement("p");
                        studentInfo.textContent = `${student.name} - ${student.email}`;
                        resultsDiv.appendChild(studentInfo);
                    });
                } else {
                    resultsDiv.innerHTML += "<p>No students found for this batch.</p>";
                }
            })
            .catch(error => console.error("Error fetching students:", error));
    } else {
        alert("Please select an institution and enter a batch year.");
    }
});
