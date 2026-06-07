# 💻 Skeleton Kode - Mie Ayam "Wengi 57"

Berikut adalah kerangka kode (skeleton code) berbasis PHP berorientasi objek (OOP) dan JavaScript kelas yang merepresentasikan perancangan sistem:

## A. Backend PHP Skeletons (`classes/`)

### 1. File: `MenuMieAyam.php`
```php
<?php
class MenuMieAyam {
    private $pdo;
    
    public $id;
    public $name;
    public $description;
    public $price;
    public $category;
    public $image_path;
    public $is_available;

    public function __construct(PDO $db) {
        $this->pdo = $db;
    }

    public function tambahMenu($name, $description, $price, $category, $image_path) {
        $stmt = $this->pdo->prepare("INSERT INTO menus (name, description, price, category, image_path) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $description, $price, $category, $image_path]);
    }

    public function ubahMenu($id, $name, $description, $price, $category, $image_path) {
        $stmt = $this->pdo->prepare("UPDATE menus SET name = ?, description = ?, price = ?, category = ?, image_path = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $price, $category, $image_path, $id]);
    }

    public function hapusMenu($id) {
        $stmt = $this->pdo->prepare("DELETE FROM menus WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function ubahStatusKetersediaan($id, $is_available) {
        $stmt = $this->pdo->prepare("UPDATE menus SET is_available = ? WHERE id = ?");
        return $stmt->execute([$is_available, $id]);
    }
}
?>
```

### 2. File: `Pesanan.php`
```php
<?php
class Pesanan {
    private $pdo;

    public $id;
    public $customer_name;
    public $table_number;
    public $phone_number;
    public $notes;
    public $total_amount;
    public $status;
    public $payment_method;
    public $created_at;

    public function __construct(PDO $db) {
        $this->pdo = $db;
    }

    public function buatPesanan($customer_name, $table_number, $phone_number, $notes, $total_amount, $payment_method, $items) {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("INSERT INTO orders (customer_name, table_number, phone_number, notes, total_amount, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customer_name, $table_number, $phone_number, $notes, $total_amount, $payment_method]);
            $order_id = $this->pdo->lastInsertId();

            foreach ($items as $item) {
                $item_stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
                $item_stmt->execute([$order_id, $item['menu_id'], $item['quantity'], $item['price']]);
            }

            $this->pdo->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
?>
```

## B. Client-Side JavaScript Skeletons (`assets/js/cart.js`)
```javascript
class CartItem {
    constructor(id, name, price, quantity) {
        this.id = id;
        this.name = name;
        this.price = price;
        this.quantity = quantity;
    }
}

class Cart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('cart')) || [];
    }

    addItem(id, name, price) {
        const existing = this.items.find(item => item.id === id);
        if (existing) {
            existing.quantity += 1;
        } else {
            this.items.push(new CartItem(id, name, price, 1));
        }
        this.save();
    }

    removeItem(id) {
        this.items = this.items.filter(item => item.id !== id);
        this.save();
    }

    updateQuantity(id, qty) {
        const item = this.items.find(item => item.id === id);
        if (item) {
            item.quantity = qty;
            if (item.quantity <= 0) this.removeItem(id);
        }
        this.save();
    }

    calculateTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));
        this.render();
    }

    clear() {
        this.items = [];
        this.save();
    }

    render() {
        // Logika memperbarui tampilan DOM keranjang secara dinamis
    }
}
```
