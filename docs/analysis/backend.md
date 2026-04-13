# Backend Analysis Summary

## Conclusion
`galaxiNew` has no meaningful Galaxy domain implementation yet. Backend migration should start from entities and flows, not screens.

## Core entities
- User
- Role
- Permission
- Shop
- CardHolder / Client / Worker domain entity
- Card
- CardType
- Purchase / Check
- Gift
- Service
- ServiceGroup
- ServiceRule
- History / Activity records

## Core flows
- auth and access control
- shop-scoped visibility
- issue/bind/change card
- change card type
- activate/deactivate cardholder
- register checks and accrue points
- apply blocking rules
- issue/remove gifts
- reporting queries

## Main risks
- business rules hidden in legacy PHP pages
- ambiguous domain naming
- history integrity issues
- reproducing UI without reproducing logic
