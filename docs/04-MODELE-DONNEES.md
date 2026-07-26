# Modèle de données

## agencies

- id
- name
- legal_name
- email
- phone
- address
- status
- timestamps

## users

- id
- agency_id
- name
- email
- phone
- password
- status
- timestamps

## owners

- id
- agency_id
- reference
- first_name
- last_name
- company_name
- email
- phone
- address
- identity_document
- status
- timestamps

## property_types

- id
- agency_id
- name
- description
- status

Exemples :
- appartement ;
- maison ;
- villa ;
- studio ;
- bureau ;
- magasin ;
- terrain ;
- entrepôt.

## properties

- id
- agency_id
- reference
- owner_id
- property_type_id
- title
- description
- address
- city
- neighborhood
- surface_area
- bedrooms
- bathrooms
- status

Statuts :
- available
- occupied
- maintenance
- inactive

## tenants

- id
- agency_id
- reference
- first_name
- last_name
- email
- phone
- address
- identity_document
- emergency_contact
- status

## lease_templates

- id
- agency_id
- name
- description
- content
- version
- status

## leases

- id
- agency_id
- reference
- property_id
- tenant_id
- template_id
- start_date
- end_date
- rent_amount
- charges_amount
- payment_due_day
- deposit_amount
- status
- signed_at
- terminated_at

## rent_schedules

- id
- agency_id
- lease_id
- period
- due_date
- expected_amount
- paid_amount
- remaining_amount
- status

## payments

- id
- agency_id
- rent_schedule_id
- amount
- payment_date
- payment_method
- reference
- proof_document
- status

## deposits

- id
- agency_id
- lease_id
- expected_amount
- received_amount
- retained_amount
- refunded_amount
- refund_date
- status

## arrears

- id
- agency_id
- lease_id
- rent_schedule_id
- tenant_id
- amount_due
- amount_paid
- remaining_amount
- first_overdue_date
- status
- severity

## reminders

- id
- agency_id
- arrears_id
- channel
- sent_at
- status
- content

## notifications

- id
- agency_id
- recipient_type
- recipient_id
- type
- channel
- subject
- content
- sent_at
- status

## documents

- id
- agency_id
- documentable_type
- documentable_id
- type
- name
- path
- mime_type
- size
- uploaded_by

## audit_logs

- id
- agency_id
- user_id
- action
- entity_type
- entity_id
- old_values
- new_values
- ip_address
- user_agent
- created_at
