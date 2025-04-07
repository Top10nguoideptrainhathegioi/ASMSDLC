let cartIcon = document.querySelector('.cart-icon');
let cartModel = document.querySelector('.cart-model');
let cartClose = document.querySelector('.close-btn');

cartIcon.onclick = () => {
    cartModel.classList.add('open-cart');
}


let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

function addToCart(productCard){


const name = productCard.querySelector('.product-name').textContent;
const priceText = productCard.querySelector('.product-price').textContent;
const price = parseFloat(priceText.replace(/[^\d.-]/g, '').trim());
const imgSrc = productCard.querySelector('.product-image').src;

const existingItem = cartItems.find((item) => item.name === name);
if(existingItem){
    existingItem.quantity +=1;

}else{
    cartItems.push({
        name,
        price,
        quantity: 1,
        image: imgSrc,
    })
}
updateLocalStorage();

}

function updateLocalStorage(){
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
}



