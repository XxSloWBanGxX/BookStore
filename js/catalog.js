document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', (e) => {
      const card = e.target.closest('.catalog-card');
      const productId = card.dataset.id;
  
      fetch('add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `productId=${productId}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Товар додано до кошика!');
        } else {
          alert('Сталася помилка, спробуйте знову');
        }
      });
    });
  });