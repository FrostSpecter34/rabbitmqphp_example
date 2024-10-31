<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Subscription Manager</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        margin: 20px;
      }
      .sort-menu {
        margin-bottom: 20px;
      }
      .subscription-list {
        margin-top: 20px;
        font-size: 18px;
      }
      .subscription-item {
        margin-bottom: 10px;
      }
    </style>
  </head>
  <body>
    <h1>Subscription Manager</h1>
    <div class="sort-menu">
      <label for="sortOptions">Sort By:</label>
      <select id="sortOptions">
        <option value="name">Name</option>
        <option value="price">Price</option>
      </select>
    </div>

    <a href="addsub.html">Add subscription</a>

    <div class="active-subscriptions">Active Subscriptions</div>
    <div id="subscriptionList" class="subscription-list"></div>

    <script>
      
      const subscriptions = [
        { name: "Netflix", price: 15.99, expires: "2023-11-07" },
        { name: "Spotify", price: 9.99, expires: "2023-11-10" },
        { name: "Amazon Prime", price: 12.99, expires: "2023-11-15" }
      ];

      function displaySubscriptions() {
        const subscriptionList = document.getElementById("subscriptionList");
        subscriptionList.innerHTML = ""; 

        subscriptions.forEach((subscription) => {
          const item = document.createElement("div");
          item.className = "subscription-item";
          item.textContent = `${subscription.name} - $${subscription.price} - Expires on: ${subscription.expires}`;
          subscriptionList.appendChild(item);
        });
      }

      document.getElementById("sortOptions").addEventListener("change", function() {
        const selectedValue = this.value;
        subscriptions.sort((a, b) => {
          if (selectedValue === "name") {
            return a.name.localeCompare(b.name);
          } else if (selectedValue === "price") {
            return a.price - b.price;
          }
        });
        displaySubscriptions();
      });

      function checkExpirations() {
        const today = new Date();
        subscriptions.forEach(subscription => {
          const expirationDate = new Date(subscription.expires);
          const diffDays = Math.ceil((expirationDate - today) / (1000 * 60 * 60 * 24));

          if (diffDays === 7) { 
            console.log(`Reminder: ${subscription.name} subscription expires in one week!`);
            // Placeholder for server call
            // sendEmailNotification(subscription.name, subscription.expires);
          }
        });
      }

    
      displaySubscriptions();

      checkExpirations();

      // Placeholder function for sending an email (requires backend implementation)
      function sendEmailNotification(subscriptionName, expirationDate) {
        // Example of a POST request to a server-side email function
        fetch('/send-email', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            name: subscriptionName,
            expires: expirationDate
          })
        })
        .then(response => response.json())
        .then(data => console.log('Email sent:', data))
        .catch(error => console.error('Error sending email:', error));
      }
    </script>
  </body>
</html>
