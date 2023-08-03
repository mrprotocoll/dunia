//// Docs: https://dbml.dbdiagram.io/docs
//// -- LEVEL 1
//// -- Schemas, Tables and References

// Creating tables
// You can define the tables with full schema names
Table author {
  id int
  country_code int
  name varchar
  email varchar
  password varchar
  "created at" varchar
  system_user_id int [ref: > Admin.id]
  Indexes {
    (id, country_code) [pk]
  }
}

Table system_users as Admin{
  id int
  name varchar
  email varchar
  password varchar
  "created at" varchar
}

Table shipping_company as Ship{
  id int
  name varchar
  email varchar
  "created at" varchar
}

Ref: products.system_user_id > system_users.id

// If schema name is omitted, it will default to "public" schema.
Table customers as U {
  id int [pk, increment] // auto-increment
  full_name varchar
  created_at timestamp
  country_code int
}

Table countries {
  code int [pk]
  name varchar
  continent_name varchar
}

// Creating references
// You can also define relaionship separately
// > many-to-one; < one-to-many; - one-to-one; <> many-to-many
Ref: U.country_code > countries.code
Ref: author.country_code > countries.code

//----------------------------------------------//

//// -- LEVEL 2
//// -- Adding column settings

Table order_items {
  id int [pk, increment]
  order_id int [ref: > orders.id] // inline relationship (many-to-one)
  product_id int
  quantity int [default: 1] // default value
}

Ref: order_items.product_id > products.id

Table orders {
  id int [pk] // primary key
  user_id int [not null, unique]
  customer_id int [not null]
  status varchar
  created_at varchar [note: 'When order created'] // add column note
}

//----------------------------------------------//

//// -- Level 3
//// -- Enum, Indexes

// Enum for 'products' table below
Enum products_status {
  out_of_stock
  in_stock
  running_low [note: 'less than 20'] // add column note
}

Enum shipping_status {
  processing
  shipped_for_delivery
  delivered // add column note
}

// Indexes: You can define a single or multi-column index
Table products {
  id int [pk]
  name varchar
  author_id int [not null]
  system_user_id int [not null]
  price int
  status products_status
  created_at datetime [default: `now()`]

  Indexes {
    (author_id, status) [name:'product_status']
    id [unique]
  }
}

Table categories {
  id int [pk]
  name varchar
}

Table product_categories {
  id int [pk]
  product_id int
  category_id int
}

Table shippings {
  id int [pk]
  order_id int [ref: > orders.id]
  shipping_company_id int [ref: > Ship.id]
  address varchar
  delivery_date  varchar
  status int
  created_at timestamp
}

Ref: product_categories.product_id > products.id
Ref: product_categories.category_id > categories.id

Table tags {
  id int [pk]
  name varchar
}

Table product_tags {
  id int [pk]
  product_id int
  tag_id int

}

Ref: product_tags.product_id > products.id
Ref: product_tags.tag_id > tags.id

Ref: products.author_id > author.id // many-to-one
Ref: product_tags.id <> products.id // many-to-many
//composite foreign key
