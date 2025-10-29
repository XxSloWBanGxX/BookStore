document.addEventListener('DOMContentLoaded', () => {

    // Обробка кнопок + і -
    document.querySelectorAll('.quantity-control').forEach(control => {
      const input = control.querySelector('.quantity-input');
      const id = control.dataset.id;
  
      control.querySelector('.plus').addEventListener('click', () => {
        let current = parseInt(input.value);
        current++;
        updateQuantity(id, current, input);
      });
  
      control.querySelector('.minus').addEventListener('click', () => {
        let current = parseInt(input.value);
        if (current > 1) {
          current--;
          updateQuantity(id, current, input);
        }
      });
    });
  
    // Функція для оновлення кількості на сервері
    function updateQuantity(id, quantity, input) {
      fetch('update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, quantity })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          input.value = quantity;
          updateCartTotal(data.total);
        } else {
          alert('Помилка оновлення кількості');
        }
      });
    }

  // Видалення товару з кошика
  document.querySelectorAll('.cart-item-remove').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.dataset.id;
      fetch('remove_from_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Видаляємо елемент зі сторінки
          button.closest('.cart-item').remove();
          updateCartTotal(data.total);
          if (data.empty) {
            document.querySelector('.cart-container').innerHTML = '<p class="cart-empty">Кошик порожній</p>';
          }
        } else {
          alert('Помилка видалення товару з кошика');
        }
      });
    });
  });

  // Кнопка Купити
  const buyButton = document.getElementById('buy-button');
  if (buyButton) {
    buyButton.addEventListener('click', () => {
      fetch('buy.php', { method: 'POST' })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Дякуємо за покупку! Ваше замовлення успішно оформлене.');
          // Очищаємо кошик на сторінці
          document.querySelector('.cart-container').innerHTML = '<p class="cart-empty">Кошик порожній</p>';
        } else {
          alert('Сталася помилка при оформленні покупки. Спробуйте пізніше.');
        }
      });
    });
  }

  // Функція оновлення загальної суми на сторінці
  function updateCartTotal(total) {
    const totalElem = document.querySelector('.cart-total');
    if (totalElem) {
      totalElem.textContent = `Загальна вартість: ${total.toFixed(2)} грн`;
    }
  }
});
