# Project

----

- [Concept](#concept)

<a name="concept"></a>

## Concept

### User creates a project
    - User configures environment with source provider secrets etc.
    - User defines which servers to deploy the project to, by default all.
    - User configures source provider to hit the /api/projects/{id}/deploy webook on push received.
    
### Source repository hits deploy endpoint
    - App creates a new deployment with the latest tarball url
    - Loops the configured Server(s), and creates a new task attached to the each server
        - Task then interacts with the server through SSH
            - execute deployment script 
            - record output & exitcode on Task
