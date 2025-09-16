function createContactsTable() {
  const container = document.getElementById("contacts-table-container");
  if (container) {
    const table = document.createElement("table");
    table.id = "contacts-table";

    const headerRow = document.createElement("tr");
    headerRow.classList.add("contacts-row");

    const headers = ["First", "Last", "Email", "Phone"];

    headers.forEach(headerText => {
      const th = document.createElement("th");
      th.textContent = headerText;
      headerRow.appendChild(th);
    });

    table.appendChild(headerRow);

    return table;
  }
}

function displayContactFetchFailure() {
  const container = document.getElementById("contacts-table-container");
  if (container) {

    container.innerHTML = "";

    const failMessage = document.createElement("p");

    failMessage.textContent = "Connection fail";
    failMessage.style.color = "red";
    failMessage.style.textAlign = "center";

    container.appendChild(failMessage);
  }
}

function addContactToTable(table, contact) {
  // Make row
  const row = document.createElement("tr")

  row.classList.add("contacts-row")

  // Add shit to row
  for (const attr in contact) {
    const column = document.createElement("td")

    column.innerHTML = contact[attr]

    row.appendChild(column)
  }

  // Add row to table
  table.appendChild(row)
}

// Get the contacts list
console.log("Fetching contacts...")
fetch("/api/GetContacts.php", {
  method: "GET",
  credentials: "include"
})
  .then((res) => { return res.json() })
  .then((resJson) => {
    if (resJson.success) {
      // Create and add contacts to table
      contactsTable = createContactsTable()
      resJson.contacts.forEach((contact) => {
        addContactToTable(contactsTable, contact)
      })
    }
    else {
      displayContactFetchFailure()
    }
  })
  .catch((err) => {
    console.log("Could not fetch contacts", err)
    displayContactFetchFailure
  })
