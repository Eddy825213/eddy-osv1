// Fetch the stats data from the JSON file
fetch('./stats.json')
  .then(response => response.json())
  .then(data => {
    const statsContainer = document.querySelector('.stats-grid');

    // Loop through each skill and create HTML elements dynamically
    data.skills.forEach(skill => {
      const statItem = document.createElement('div');
      statItem.classList.add('stat-item');

      // Create skill name with icon
      const statName = document.createElement('div');
      statName.classList.add('stat-name');
      statName.innerHTML = `<i class="${skill.icon}"></i> ${skill.name}`;
      statItem.appendChild(statName);

      // Create skill level bar
      const statBar = document.createElement('div');
      statBar.classList.add('stat-bar');
      const statFill = document.createElement('div');
      statFill.classList.add('stat-fill');
      statFill.style.width = `${skill.level}%`;
      statBar.appendChild(statFill);
      statItem.appendChild(statBar);

      // Create skill title (level)
      const statValue = document.createElement('div');
      statValue.classList.add('stat-value');
      statValue.textContent = `Lv. ${skill.level} - ${skill.title}`;
      statItem.appendChild(statValue);

      // Append the stat item to the container
      statsContainer.appendChild(statItem);
    });
  })
  .catch(error => console.error('Error loading the stats:', error));
