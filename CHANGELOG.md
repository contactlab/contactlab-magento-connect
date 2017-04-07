# Changelog

## 2.5.4 (2017-04-07)
- **Improvement** Added SFTP read/write test in check configuration page.
- Bug fixes.

## 2.5.3 (2017-01-17)
- Bug fixes.

## 2.5.2 (2017-01-10)
- **Improvement** Fix to subscribers "created_at" field in exported data.

## 2.5.1 (2016-12-15)
- Bug fixes.

## 2.5.0 (2016-12-13)
- **Added compatibilty to laters 5.* php version**
- Bug fixes.

## 2.4.2 (2016-10-28)
- **Improvement** Fix table prefix.
- **Improvement** Duplicated Customers Check.
- **Improvement** Change mail separator from , to ;
- **Improvement** Add field created_at to XML export customer.
- Bug fix.

## 2.4.1 (2016-10-06)
- **New feature** Made custom subscription form visibility configurable via settings page.
- **Improvement** Custom table prefix is now fully supported.
- **Changed** Task data now relies on a blob field to allow binary (attachment) support.
- Bug fix.

## 2.4.0 (2016-07-19)
- **New feature** Added config option to show custom newsletter subscribe form.
- **New feature** Added config option to send transactional emails immediatly after insert.
- Bug fix.

## 2.3.1 (2016-04-04)
- Generate subscribers fields record if not present, in edit page

## 2.3 (2016-01-20)

- **New feature** Custom subscribers fields added to frontend.
- **Improved** New checks available into the "Configuration checks" page.
- **Improved** Runs essential checks before subscriber exports.
- **Improved** Added "customer_group_id" and "customer_group_name" to customers' export.
- Modified adminhtml controllers due to SUPEE-6788 Magento patch.


## 2.2.7 (2015-09-30)

- Fixed a bug where previously inserted newsletter templates would not be displayed. ([3c8c906](https://github.com/contactlab/contactlab-magento-connect/commit/3c8c906c6a7beb43d77d313ccc2cfdad28474139))


## 2.2.6 (2015-09-29)

- Templates may now be associated to a specific store view ([831d375](https://github.com/contactlab/contactlab-magento-connect/commit/831d375c550d4f52a2c361d71d55a1aee5193443))


## 2.2.5 (2015-09-08)

- ContactLab Connect for Magento is now open source on [https://github.com/contactlab/contactlab-magento-connect](https://github.com/contactlab/contactlab-magento-connect).
- Per store subscription status is supported in multi store configurations.
- Increased export capabilities for large data sets.
- Templates size limit constraint removed.
- Improved readability of XML Delivery status reports - ([#1](https://github.com/contactlab/contactlab-magento-connect/issues/1))
- Minor other improvements.
