Thoughts on GDPR API
--------------------

Goals
^^^^^

- Make default TYPO3 core GDPR compliant
- Enable website owners to be GDPR compliant
- Empower developers to create GDPR compliant extensions
- Enable integrators to configure extensions to be GDPR compliant

Terms/Usages
^^^^^^^^^^^
- Anonymization can happen:
    - On persisting ☐
    - On recurring basis (scheduler) ✔
    - On manual interaction ✔
- Randomization can happen:
    - On recurring basis (scheduler) ✔
    - On manual interaction ✔
- Hiding data can happen
    - On manual interaction ✔
- Data Removal can happen:
    - On recurring basis (scheduler) ✔
    - On manual interaction ✔
- Data Retrieval can happen:
    - On manual interaction ✔

Stakeholders
^^^^^^^^^^^^
- TYPO3 Core
    - IP addresses need to be anonymized
        - Logs
        - Indexed_search
    - Give overview over sensitive data & place for interaction (removal, retrieval, ...)
    - Persisting “User last logged in” needs to be configurable
- Website owner
    - As a website owner I want to be able to remove all personal data collected for a person.
    - As a website owner I want to be able to extract all personal data collected for a person.
    - As a website owner I want to be able to randomize all personal data collected for a person.
    - As a website owner I want my data privacy guideline to be automatically created based on my current API configuration.
    - As a website owner I want to know who has access to the data
    - As a website owner I want to be able to decide on a per-user basis which backend user can see, access, randomize, export or release personalized data.
- Developers
    - As a developer I want to be able to pipe my persistence through a dedicated API that can be configured to follow GDPR rules
- Integrators
    - As an integrator I want to be able to configure each API usage to fit my current requirements.
