import Vue from 'vue'
import numeral from 'numeral';

import Variations from './components/Variations'
import Variation from './components/Variation'
import Cart from './components/Cart'
import MiniCart from './components/MiniCart'

window.Vue = Vue;

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

export const productEvents = new Vue({});

Vue.component('product-variations', Variations);
Vue.component('product-variation', Variation);
Vue.component('cart', Cart);
Vue.component('mini-cart', MiniCart);

Vue.filter('toCurrency', value => {
  if (value) {
    const v = parseFloat(value);
    return new Intl.NumberFormat('en-US', {minimumFractionDigits: 2}).format(v.toFixed(2))
  }

  return 0;
});

export const productManager = new Vue({
  el: '#product-manager',
  methods: {
    updateTotals(items, totals, cart, config) {
      const newTotals = {... totals};
      const subTotal = numeral(0);

      items.forEach(item => {
        subTotal.add(item.total);
      });

      newTotals.sub_total = subTotal.value();

      const total = numeral(0);
      total.add(subTotal.value());

      if (cart.discount) {
        let discount = 0;
        if (cart.discount.amount) {
          discount = cart.discount.amount;
        }

        if (cart.discount.percent) {
            const rate = numeral(cart.discount.percent).divide(100).value();
            const subTotal = total.clone().value();
            discount = numeral(subTotal).multiply(rate).value();
        }

        newTotals.discount = discount;
        total.subtract(discount);
      }

      if (config.orders.active && cart.delivery) {
        newTotals.delivery = cart.delivery.zone.price;
        let price = cart.delivery.zone.price;
        if (cart.delivery.zone.delivery_conditions && cart.delivery.zone.delivery_conditions.length) {
          cart.delivery.zone.delivery_conditions.forEach(condition => {
            const rule = `cart.totals.${condition.option} ${condition.is} ${condition.value}`;
            if (eval(rule)) {
              price = condition.price;
              cart.delivery.zone.label = 0;
            } else {
              cart.delivery.zone.label = cart.delivery.zone.price;
            }
          });
        }
        total.add(price);
      }


      if (cart.extra_fees && cart.extra_fees.length) {
        cart.extra_fees.forEach(fee => {
          let feeTotal = 0;
          if (fee.percent) {
            const rate = numeral(fee.percent).divide(100).value();
            const subTotal = total.clone().value();
            feeTotal = numeral(subTotal).multiply(rate).value();
          }

          if (fee.value) {
            feeTotal = fee.value;
          }

          fee.total = feeTotal;

          total.add(feeTotal);
        });
      }

      if (config.orders.gst.active) {
        const rate = numeral(config.orders.gst.percent).divide(100).value();
        const subTotal = total.clone().value();
        const gst = numeral(subTotal).multiply(rate).value();
        newTotals.gst = gst;

        if (config.orders.gst.type === 'ex') {
          total.add(gst);
        }
      }

      newTotals.total = total.value();

      return newTotals;
    },

  }
});

const postcodeField = document.querySelector('.cart__field--postcode input');
if (postcodeField) {
  postcodeField.addEventListener('keyup', event => {
    const time = setTimeout(() => {
      productEvents.$emit('products.checkout.postcode', event.target.value);
      clearTimeout(time);
    }, 200);
  });
}
