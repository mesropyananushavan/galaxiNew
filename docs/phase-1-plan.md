# Phase 1 Plan

## Objective
Turn `galaxiNew` from generic Laravel starter into Galaxy-specific application foundation.

## Work items
1. Define admin navigation map based on `galaxiOld`
2. Define initial domain entity map
3. Add authorization baseline
4. Add shop-scoped access baseline
5. Add first migrations and model skeletons
6. Align admin shell structure toward old UI map

## Initial entity set
- Shop
- Role
- Permission
- CardHolder
- CardType
- Card

## Definition of done
- core entities exist as Laravel models/migrations
- admin shell reflects target sections
- authorization baseline exists
- project is visibly no longer an empty starter
