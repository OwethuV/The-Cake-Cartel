-- Create the database
CREATE DATABASE IF NOT EXISTS DessertECommerce;
USE DessertECommerce;

-- Create USERS table
CREATE TABLE IF NOT EXISTS USERS (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    cell VARCHAR(20),
    address TEXT,
    password VARCHAR(255) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create PRODUCTS table
CREATE TABLE IF NOT EXISTS PRODUCTS (
    productId INT AUTO_INCREMENT PRIMARY KEY,
    productName VARCHAR(100) NOT NULL,
    productImg VARCHAR(255),
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    flavor VARCHAR(50),
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create CART table
CREATE TABLE IF NOT EXISTS CART (
    cartId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    productId INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    cartPrice DECIMAL(10, 2) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES USERS(userId) ON DELETE CASCADE,
    FOREIGN KEY (productId) REFERENCES PRODUCTS(productId) ON DELETE CASCADE
);

-- Create ORDERS table
CREATE TABLE IF NOT EXISTS ORDERS (
    orderId INT AUTO_INCREMENT PRIMARY KEY,
    cartId INT NOT NULL,
    deliveryPrice DECIMAL(10, 2) NOT NULL,
    totalPrice DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES USERS(userId) ON DELETE CASCADE
);

-- Create ORDER_ITEMS table
CREATE TABLE IF NOT EXISTS ORDER_ITEMS (
    orderItemId INT AUTO_INCREMENT PRIMARY KEY,
    orderId INT NOT NULL,
    productId INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (orderId) REFERENCES ORDERS(orderId) ON DELETE CASCADE,
    FOREIGN KEY (productId) REFERENCES PRODUCTS(productId) ON DELETE CASCADE
);

-- Create password_resets table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserting products into the PRODUCTS table
INSERT INTO PRODUCTS (productName, productImg, price, description, flavor) VALUES
('Chocolate Fudge Cake', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/chocolate-fudge.webp', 249.99, 'Indulge in a slice of pure decadence with our chocolate fudge cake.
Each bite is a journey into rich, velvety perfection. We have crafted a cake with a dense, moist crumb, packed with the deep, satisfying flavor of high-quality dark chocolate. Its then generously blanketed in our signature fudge frosting—a thick, glossy, and intensely chocolatey layer that melts in your mouth.
This isnt just a cake, its an experience. The harmonious blend of the tender cake and the smooth, luxurious fudge creates an unforgettable taste that will leave you wanting more. Its the perfect treat for any occasion, or simply for satisfying that craving for something truly extraordinary. ', 'Chocolate'),
('Red Velvet Cupcakes (6)', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/red-velvet-cupcakes.webp', 179.99, 'Savor the elegance of our red velvet cupcakes.
These are not just cupcakes; they are a celebration of classic flavor and sophisticated charm. Each one features a perfectly baked, deeply red cake with a subtle hint of cocoa, offering a delicate and moist crumb thats absolutely irresistible.
The true highlight is the frosting: a swirl of rich, tangy cream cheese frosting that’s both light and luxurious. Its the perfect complement to the mild, chocolatey cake, creating a harmonious balance of flavors. Finished with a sprinkle of red velvet crumbs, these cupcakes are a feast for both the eyes and the palate. They are the ideal treat for any occasion, promising a touch of sweet sophistication with every bite', 'Red Velvet'),
('New York Cheesecake', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/ny-cheesecake.webp', 299.99, 'Experience the icon of creamy indulgence with our classic New York Cheesecake.
This is the real deal—a cheesecake that is rich, dense, and luxuriously smooth. We start with a buttery graham cracker crust, a perfect foundation for the star of the show: a thick, velvety filling made with a generous amount of premium cream cheese.
Baked to a golden perfection, each slice delivers a subtle tang that balances the rich sweetness, creating a flavor profile that is both decadent and refreshing. A true crowd-pleaser, our New York cheesecake is perfect on its own or can be topped with fresh berries, a chocolate drizzle, or a caramel sauce for an extra touch of delight. ', 'Vanilla'),
('Salted Caramel Tart', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/salted-caramel-tart.webp', 229.99, 'Dive into a perfect harmony of sweet and salty with our salted caramel tart.
This exquisite dessert begins with a buttery, crisp pastry shell that provides a delicate crunch in every bite. The star of the show is the filling: a luscious, gooey layer of rich caramel, perfectly balanced with a sprinkle of flaky sea salt. This touch of salt elevates the caramels sweetness, creating a complex and utterly addictive flavor profile.
Each forkful is a symphony of textures and tastes—the tender crust, the smooth, decadent caramel, and the subtle pop of salt. Its a sophisticated twist on a classic treat, designed to captivate your senses and satisfy your deepest cravings. ', 'Caramel'),
('Lemon Meringue Pie', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/lemon-meringue.webp', 259.99, 'Prepare your taste buds for a symphony of textures and flavors with our Lemon Meringue Pie.
This classic dessert is a masterpiece of delicious contrasts. It starts with a delicate, flaky crust that cradles a vibrant, sun-kissed lemon filling. Made with freshly squeezed lemons, this filling delivers a perfect balance of zesty tartness and sweet creaminess that will make your mouth pucker with delight
Crowning this lemon dream is a sky-high swirl of ethereal meringue. Light, fluffy, and toasted to a golden-brown perfection, it adds a cloud-like sweetness that beautifully complements the bold citrus below. Each bite is a journey from the crisp crust to the tangy filling and the sweet, airy meringue, making it a dessert thats both refreshing and utterly satisfying', 'Lemon'),
('Triple Chocolate Brownies', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/triple-chocolate-brownies.webp', 159.99, 'Get ready to experience chocolate in its most glorious form with our Triple Chocolate Brownies.
These arent just brownies; they are an absolute chocolate lovers dream, a deep and decadent fudge fest in every square. We start with a rich, dark chocolate batter that creates a fudgy, moist center, with a perfect crisp, crackly top.
But why stop at one type of chocolate? Weve loaded these brownies with generous chunks of milk and white chocolate, ensuring a surprise of creamy, melting goodness with every single bite. The result is a symphony of textures and flavors—a treat thats intensely chocolatey, perfectly sweet, and utterly irresistible. ', 'Chocolate'),
('Strawberry Shortcake', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/strawberry-shortcake.webp', 279.99, 'Delight in the fresh taste of summer with our classic Strawberry Shortcake.
This timeless dessert is a beautiful and delicious celebration of simple, pure flavors. At its heart is a light, tender shortcake—rich with butter and a hint of sweetness—that provides the perfect base for the stars of the show.
Generous layers of ripe, juicy strawberries, macerated just enough to release their vibrant flavor, are nestled between the shortcake layers. A generous dollop of our airy, lightly sweetened whipped cream tops it all off, tying every element together in a cloud of creamy perfection. Its a treat that feels both elegant and comforting, capturing the essence of fresh, sun-ripened strawberries in every delightful bite. ', 'Strawberry'),
('Tiramisu Cake', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/tiramisu-cake.webp', 269.99, 'Treat yourself to the legendary "pick me up" of Italy with our magnificent Tiramisu.
This is a carefully crafted indulgence of exquisite layers and balanced flavors. At the foundation are delicate ladyfingers, quickly dipped in a rich, potent espresso and a hint of liqueur, giving them a beautiful coffee-infused tenderness.
These layers are then intertwined with a luxurious, velvety mascarpone cream—a silky blend of sweet cream and a subtle tang that perfectly complements the coffee. The entire creation is finished with a generous dusting of rich, unsweetened cocoa powder, providing a final touch of deep chocolate that ties every element together.', 'Coffee'),
('Matcha Green Tea Cake', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/matcha-cake.webp', 289.99, 'Step into a world of subtle elegance with our Matcha Green Tea Cake.
This is a dessert that offers a sophisticated twist on the traditional sweet treat. Our cake is made with high-quality matcha, infusing the light, fluffy layers with a beautiful, earthy green hue and a distinct, yet delicate, green tea flavor. Its a taste that is both mellow and vibrant, with a pleasant hint of sweetness that is never overpowering.
To perfectly complement the cake, weve layered it with a luscious, airy cream. The combination of the slightly bitter, verdant matcha and the sweet, creamy frosting creates a beautifully balanced flavor profile that is truly unique and unforgettable. Its an experience of refined indulgence, perfect for those seeking a dessert that is as beautiful as it is delicious. ', 'Matcha'),
('Peanut Butter Cheesecake', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/peanut-butter-cheesecake.webp', 279.99, 'For the ultimate indulgence, look no further than our Peanut Butter Cheesecake.
This is a dessert thats designed for true connoisseurs of flavor. We begin with a rich and crumbly chocolate cookie crust, providing the perfect dark and decadent foundation. On top of this lies a creamy, velvety smooth cheesecake filling, generously infused with premium peanut butter to create a rich, nutty, and slightly salty tang that is simply irresistible.
Each slice is a glorious balance of sweet and savory. We finish this masterpiece with a decadent drizzle of chocolate ganache and a sprinkle of chopped peanuts, creating an unforgettable experience that is as beautiful to look at as it is delicious to eat. Its the perfect treat for anyone who loves the classic combination of peanut butter and chocolate, elevated to an entirely new level of sophistication.', 'Peanut Butter'),
('Apple Crumble', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/apple-crumble.webp', 199.99, 'Transport yourself to a world of cozy comfort with our classic Apple Crumble.
This is a warm, nostalgic embrace in a bowl. We begin with a generous helping of tender, baked apples, perfectly spiced with cinnamon and a hint of sugar, creating a sweet and juicy filling thats bubbling with flavor.
Crowning this fruit-filled base is our golden, buttery crumble topping. With its rustic, textural appeal and rich, nutty flavor, it provides a delightful contrast to the soft apples below. Each spoonful offers the perfect symphony of sweet, tart, and crunchy—a truly heartwarming dessert thats best enjoyed warm, perhaps with a scoop of vanilla ice cream or a drizzle of fresh cream.', 'Apple'),
('Pistachio Baklava', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/pistachio-baklava.webp', 239.99, 'Savor a taste of the Mediterranean with our exquisite Pistachio Baklava.
This is a true masterpiece of pastry craftsmanship, made with dozens of whisper-thin layers of flaky phyllo dough. Each delicate sheet is brushed with rich, melted butter and then stacked with a generous filling of vibrant, finely chopped pistachios.
Baked to a perfect golden crisp, the baklava is then drenched in a light, fragrant syrup that soaks into every layer, creating a delightful contrast between the crunchy pastry and the tender, nutty filling. The pistachios lend a beautiful color and a unique, earthy sweetness that is simply irresistible. This is more than a dessert—its a journey of rich flavor and satisfying texture.', 'Pistachio'),
('Raspberry Chocolate Mousse', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/raspberry-mousse.webp', 219.99, 'Indulge in a sophisticated dance of flavors with our Raspberry Chocolate Mousse.
This dessert is a celebration of two timeless partners: deep, rich chocolate and bright, tangy raspberry. We create a light-as-air dark chocolate mousse, a velvety cloud of cocoa that melts in your mouth and leaves a lingering, luxurious finish.
Swirled throughout this decadent mousse is a vibrant, homemade raspberry purée. Its tartness cuts through the richness of the chocolate, creating a delightful contrast that is both refreshing and complex. Each spoonful is a perfect blend of intense chocolate and the sweet, fruity burst of fresh raspberries. Its an elegant, unforgettable dessert thats perfect for any occasion.', 'Raspberry'),
('Carrot Cake', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/carrot-cake.webp', 249.99, 'Indulge in a classic that never goes out of style with our Carrot Cake.
This is more than just a cake—its a perfect blend of wholesome ingredients and comforting flavors. Each moist, dense slice is packed with freshly grated carrots, creating a natural sweetness and a beautifully tender texture. We carefully blend in a medley of warm spices like cinnamon and nutmeg, giving the cake a cozy, aromatic depth that is truly irresistible.
The final touch is our luscious cream cheese frosting. With its rich, tangy flavor and velvety smoothness, it provides the ideal complement to the spiced carrot cake. Its a dessert thats both hearty and refined, offering a taste of tradition with every delicious forkful.', 'Spice'),
('Chocolate Chip Cookies (12)', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/chocolate-chip-cookies.webp', 129.99, 'Sink your teeth into pure comfort with our classic Chocolate Chip Cookies.
Theres a reason this cookie is a timeless favorite. Each one is a delightful study in perfect contrasts: a crisp, golden-brown edge gives way to a soft, chewy center that melts in your mouth.
Our dough is rich and buttery, but the real stars are the generous heaps of chocolate chips. Weve packed every cookie with morsels of chocolate that soften and turn into gooey pockets of pure bliss as they bake. Whether you enjoy them warm with a glass of milk or on their own, our chocolate chip cookies are a perfect taste of simple, irresistible joy.', 'Chocolate'),
('Mango Passionfruit Tart', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/mango-tart.webp', 239.99, 'Savor a slice of sunshine with our exquisite Mango Passionfruit Tart.
This tropical delight is a symphony of vibrant, exotic flavors. A buttery, crisp tart shell provides the perfect foundation for a smooth and velvety mango and passionfruit crémeux. The sweet, juicy notes of ripe mango are beautifully balanced by the tangy, bright burst of passionfruit, creating a flavor that is both intensely fruity and wonderfully refreshing.
Each bite offers a blissful escape to the tropics, with a perfect harmony of textures—from the satisfying snap of the crust to the silky, smooth filling. Its an elegant, beautiful, and unforgettable dessert that will leave you dreaming of warmer days.', 'Mango'),
('Cinnamon Rolls (6)', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/cinnamon-rolls.webp', 149.99, 'Treat yourself to the irresistible warmth of our classic Cinnamon Rolls.
These are more than just a pastry; they are a comforting, aromatic indulgence that fills the air with the cozy scent of cinnamon. We start with a soft, pillowy dough, rolled into a perfect spiral and generously spread with a rich, buttery cinnamon-sugar filling. As they bake, the cinnamon caramelizes into a gooey, fragrant masterpiece.
But the true magic happens when they are fresh from the oven, drizzled with a thick, sweet glaze that melts into every nook and cranny. Each bite is a delightful combination of soft dough, gooey cinnamon filling, and sweet, creamy icing. It’s the perfect way to start your day or end it with a sweet, comforting treat.', 'Cinnamon'),
('Chocolate Hazelnut Éclairs', 'https://raw.githubusercontent.com/OwethuV/product_pictures/main/eclairs.webp', 199.99, 'Unleash your inner connoisseur with our exquisite Chocolate Hazelnut Éclairs.
This is a true French classic, elevated to a new level of pure indulgence. We begin with a delicate, airy choux pastry shell, baked to a perfect golden crispness. Each éclair is then generously filled with a rich, velvety chocolate hazelnut cream, a blend so smooth and decadent its like a dream.
The finishing touch is a luxurious dark chocolate ganache, which coats the top with a glossy, deep cocoa shine. A final sprinkle of crunchy, toasted hazelnuts adds a delightful texture and a nutty, aromatic finish. Its a symphony of textures and flavors—from the crisp pastry and creamy filling to the rich chocolate and satisfying crunch—that will transport you /straight to a Parisian patisserie.', 'Hazelnut');

