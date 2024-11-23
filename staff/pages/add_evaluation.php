<?php

if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('Evaluation form updated successfully');
        });
    </script>";
}

include '../../config/config.php';
session_start();

$seminar_id = $_GET['seminar_id'] ?? null;
if (!$seminar_id) {
    die("Invalid seminar ID.");
}

// Fetch all custom fields
$stmt = $conn->prepare("SELECT * FROM evaluation_fields WHERE seminar_id = ?");
$stmt->bind_param("i", $seminar_id);
$stmt->execute();
$fields = $stmt->get_result();
$stmt->close();

// Fetch evaluation instructions if available
$instructions_stmt = $conn->prepare("SELECT evaluation_instructions FROM seminars WHERE seminar_id = ? LIMIT 1");
$instructions_stmt->bind_param("i", $seminar_id);
$instructions_stmt->execute();
$instructions_result = $instructions_stmt->get_result();
$instructions = $instructions_result->fetch_assoc()['evaluation_instructions'] ?? '';
$instructions_stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Evaluation Form</title>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-update {
            background-color: #B19CD9;
            color: white;
        }

        .btn-update:hover {
            background-color: #9d8cb8;
        }

        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            overflow: auto;
            max-height: 90vh;
        }

        .bg-light {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
    </style>
</head>

<body class="bg-light py-5 d-flex justify-content-center">
    <div class="bg-white p-4 rounded shadow w-100" style="max-width: 800px;">
        <h1 class="text-center fs-3 fw-bold mb-4">Evaluation Form</h1>

        <form id="evaluation-form" action="save_evaluation.php" method="POST">
            <input type="hidden" name="seminar_id" value="<?php echo htmlspecialchars($seminar_id); ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold text-muted">Evaluation Instructions:</label>
                <textarea class="form-control" id="instructions-textarea" name="evaluation_instructions"
                    rows="3"><?php echo htmlspecialchars($instructions); ?></textarea>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-danger" onclick="openModal()">Add Field</button>
            </div>

            <div class="mb-3" id="fields-container">
                <?php while ($field = $fields->fetch_assoc()): ?>
                    <div class="d-flex align-items-center mb-3" id="field-<?php echo $field['field_id']; ?>">
                        <div class="flex-grow-1">
                            <label class="form-label">
                                <?php echo htmlspecialchars($field['field_label']); ?>
                                <?php if ($field['required']): ?><span class="text-danger">*</span><?php endif; ?>
                            </label>

                            <?php if ($field['field_type'] === 'text'): ?>
                                <input type="text" class="form-control" disabled>
                            <?php elseif ($field['field_type'] === 'textarea'): ?>
                                <textarea class="form-control" rows="3" disabled></textarea>
                            <?php elseif ($field['field_type'] === 'radio' || $field['field_type'] === 'dropdown'): ?>
                                <?php foreach (json_decode($field['options']) as $option): ?>
                                    <div class="form-check">
                                        <input type="<?php echo $field['field_type'] === 'radio' ? 'radio' : 'checkbox'; ?>"
                                            class="form-check-input" disabled>
                                        <label class="form-check-label"><?php echo htmlspecialchars($option); ?></label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <i class="bi bi-trash text-danger ms-2" role="button"
                            data-field-id="<?php echo $field['field_id']; ?>" style="cursor: pointer;"></i>
                    </div>
                <?php endwhile; ?>
            </div>

            <button type="submit" class="btn btn-update w-100" id="update-button">Update Evaluation</button>
        </form>
    </div>

    <div class="modal fade" id="fieldModal" tabindex="-1" aria-labelledby="fieldModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fieldModalLabel">Add New Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-field-form">
                        <label class="form-label fw-semibold">Field Label:</label>
                        <input type="text" id="field-label" class="form-control mb-3" placeholder="Field label..."
                            required>

                        <label class="form-label fw-semibold">Field Type:</label>
                        <select id="field-type" class="form-select mb-3" onchange="toggleChoices()">
                            <option value="checkbox">Checkbox</option>
                            <option value="dropdown">Dropdown</option>
                            <option value="radio">Radio</option>
                            <option value="textarea">Textarea</option>
                            <option value="number">Number</option>
                            <option value="text">Text</option>
                        </select>

                        <div class="form-check mb-3">
                            <input type="checkbox" id="field-required" class="form-check-input">
                            <label class="form-check-label" for="field-required">Required</label>
                        </div>

                        <div id="choices-section" class="d-none">
                            <label class="form-label">Options:</label>
                            <div id="options-container">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control option-input" placeholder="Option 1">
                                    <button type="button" class="btn btn-outline-danger remove-option">×</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-danger" onclick="addChoice()">Add another
                                choice</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-update w-100" onclick="submitField()">SUBMIT</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        const fieldModal = new bootstrap.Modal(document.getElementById('fieldModal'));

        // Function to open the modal for adding a new field
        function openModal() {
            document.getElementById("add-field-form").reset();
            toggleChoices();
            fieldModal.show();
        }

        // Function to toggle the display of the options input section based on field type
        function toggleChoices() {
            const fieldType = document.getElementById('field-type').value;
            const choicesSection = document.getElementById('choices-section');
            choicesSection.classList.toggle('d-none', fieldType !== 'dropdown' && fieldType !== 'radio');
        }

        // Function to dynamically add a new choice option for dropdown or radio fields
        function addChoice() {
            const optionsContainer = document.getElementById('options-container');
            const optionInput = document.createElement('div');
            optionInput.className = 'input-group mb-2';
            optionInput.innerHTML = `
                <input type="text" class="form-control option-input" placeholder="New Option">
                <button type="button" class="btn btn-outline-danger remove-option">×</button>
            `;
            optionsContainer.appendChild(optionInput);

            optionInput.querySelector('.remove-option').addEventListener('click', () => {
                optionsContainer.removeChild(optionInput);
            });
        }

        // Function to submit a new field to the backend
        function submitField() {
            const fieldLabel = document.getElementById('field-label').value;
            const fieldType = document.getElementById('field-type').value;
            const isRequired = document.getElementById('field-required').checked;
            let options = [];

            // Collect options if the field type is dropdown or radio
            if (fieldType === 'dropdown' || fieldType === 'radio') {
                document.querySelectorAll('.option-input').forEach(input => {
                    if (input.value) options.push(input.value);
                });
            }

            fetch('add_field_evaluation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    seminar_id: <?php echo json_encode($seminar_id); ?>,
                    field_label: fieldLabel,
                    field_type: fieldType,
                    required: isRequired,
                    options: options
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Field added successfully!");

                        const newFieldHTML = `
                            <div class="d-flex align-items-center mb-3" id="field-${data.field_id}">
                                <div class="flex-grow-1">
                                    <label class="form-label">${fieldLabel}${isRequired ? '<span class="text-danger">*</span>' : ''}</label>
                                    ${fieldType === 'text' ? '<input type="text" class="form-control" disabled>' : ''}
                                    ${fieldType === 'textarea' ? '<textarea class="form-control" rows="3" disabled></textarea>' : ''}
                                    ${fieldType === 'radio' || fieldType === 'dropdown' ? options.map(option => `
                                        <div class="form-check">
                                            <input type="${fieldType === 'radio' ? 'radio' : 'checkbox'}" class="form-check-input" disabled>
                                            <label class="form-check-label">${option}</label>
                                        </div>`).join('') : ''}
                                </div>
                                <i class="bi bi-trash text-danger ms-2" role="button" data-field-id="${data.field_id}" style="cursor: pointer;"></i>
                            </div>`;

                        document.getElementById("fields-container").insertAdjacentHTML("beforeend", newFieldHTML);
                    } else {
                        alert("Error adding field: " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred. Please try again.");
                });

            fieldModal.hide();
        }

        // Function to delete a field
        function deleteField(fieldId) {
            if (!fieldId) {
                console.error("Field ID is undefined.");
                alert("Unable to delete field. Please try again.");
                return;
            }

            if (confirm("Are you sure you want to delete this field?")) {
                fetch('delete_field_evaluation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ field_id: fieldId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Field deleted successfully!");
                            const fieldElement = document.getElementById(`field-${fieldId}`);
                            if (fieldElement) {
                                fieldElement.remove(); // Remove the field from DOM
                            }
                        } else {
                            alert("Error deleting field: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("An error occurred. Please try again.");
                    });
            }
        }

        // Add event delegation for dynamically added trash icons
        document.getElementById('fields-container').addEventListener('click', function (event) {
            if (event.target.classList.contains('bi-trash')) {
                const fieldId = event.target.getAttribute('data-field-id');
                if (fieldId) {
                    deleteField(fieldId);
                } else {
                    console.error("Field ID is undefined or missing.");
                    alert("Unable to delete field. Please reload the page and try again.");
                }
            }
        });

        // Handle form submission and page reload warnings
        document.getElementById("evaluation-form").addEventListener("submit", function () {
            window.removeEventListener("beforeunload", handleBeforeUnload);
        });

        function handleBeforeUnload(event) {
            event.preventDefault();
            event.returnValue = 'Are you sure you want to reload the page? Any unsaved changes will be lost.';
        }

        window.addEventListener("beforeunload", handleBeforeUnload);

    </script>

</body>

</html>