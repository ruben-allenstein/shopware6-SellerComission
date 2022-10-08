# Shopware 6 SellerComission plugin

| Version | Changes                                              | Availability   |
|---------|-------------------------------------------------------- |----------------|
| 1.0.0   | Initial release                                       | Github         |
| 1.1.0   | Fixed redirect resolving within multiple seo urls     | Github         |

# Installation

## Install with composer

* Change to your root Installation of shopware
* Run command `composer require swag/seller-comission` - install and active plugin with Plugin Manager

## Plugin:
- This feature is if you want to give your employees a seller comission
- e.g. When they advise the customers of your shop and make a purchase with his/her administration account for the customer

## Features of this plugin
- New custom field called "employee number"
- New custom field called "seller comission"
- automatically set employee number in seller comission if the employee makes the purchase
- employee number can entered later manually aswell


## How To
- You give every employee with administration access an id in a new created custom field called "employee number"
- If the employee creates a new purchase he gets automatically the sellercomission
- If the customer will be advised by the employee, you can subsequently tick the commission if you click on this order in the administration
