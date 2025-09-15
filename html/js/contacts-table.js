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

example = [
  {
    "first": "Rafael",
    "last": "Niebles",
    "email": "a@gmail.com",
    "phone": "786 416 4161",
  },
  {
    "first": "Lily",
    "last": "Goodman",
    "email": "b@gmail.com",
    "phone": "I wish",
  }
]

// NOTE: Test
contactsTable = document.querySelector("#contacts-table tbody")
console.log("Hello")

example.forEach((contact) => {
  addContactToTable(contactsTable, contact)
})
