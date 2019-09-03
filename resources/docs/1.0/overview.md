# Overview

---

- [Hierachy](#hierachy)
- [Example](#example)
- [Admin](#admin)

<a name="hierachy"></a>
## Model Hierachy

[//]: # "Encrypted Environment Variables that'll get pushed to the servers ~/.profile or registry:HKEY_LOCAL_MACHINE\System\CurrentControlSet\Control\Session Manager\Environment incase of windows."
Environment ( encryption_key, variables )

[//]: # "SSH Keys"
[//]: # "KeyPairs can be owned by both a team and individual user(s). This allows for deploying multiple keys to a single server at once."
KeyPair ( owner_type, owner_id, encryption_key, public_key, private_key )

[//]: # "Team is like an Organization, that 'owns' a set of clouds -> servers -> projects"
[//]: # "Deployments represent pushing code from the project repository to one or multiple servers"
[//]: # "Being the owner of the team owning a cloud is synonymous with being an admin within the scope of the team; bypassing all permissions by default."
[//]: # "A project encapsulates the source origin and methods of aquiring for deployment(s)."
User
    - Team ( name, owner_id ) : events [created,updated,deleted]
        - TeamMember ( team_id, user_id, permissions ) : events [created,updated,deleted]
        - TeamInvitation ( team_id, creator_id, recipient_id, token, sent_at, accepted_at ) : events [created,updated,deleted,sent,accepted,declined]
        - Projects ( team_id, environment_id, vcs, repository_url ) : events [created,updated,deleted] 

[//]: # "Cloud aka. Server provider. Eg. DigitalOcean"
[//]: # "Servers aka. Droplets in DigitalOcean"
Cloud ( team_id, environment_id, provider, private_network, address ) : events [created,updated,deleted]
    - Servers ( environment_id, cloud_id, status, image, private_address, public_address ) : events [created,updated,deleted]
        - Jobs: CreateServerInCloud, DeleteServerInCloud
        - ServerKeyPairs ( server_id, key_pair_id )
        - Deployments ( server_id, project_id, script, exitcode, output ) : events [created,updated,deleted,executing,executed]
            - Realms ( server_id, deployment_id, name, url, status, meta:json( wow_expansion, build ) ) : events [created,updated,deleted]

<a name="example"></a>

## Example flow

- User Registers.
- We display a listing of their current team they can choose one from, or create a new
- After selecting team, we show the current clouds on the team and if permitted, a create new cloud page.
- After selecting their cloud, they'll see a server configurations page where they select their server specs, image to deploy and amount.
    - We display an overview, requesting confirmation before actually creating the servers on the cloud provider.
- Then the user will see an overview of their projects and be able to create new project
    - By clicking create project, 
        - we ask for a source repository and check to see if we need a token in our environment to fetch it
        - we ask the user to select which servers to deploy to
        - When a project has been created, we'll create a deployment on each server

<a name="admin"></a>

## Admin API

- Authenticate + OnlyAdmins middleware
- CRUD actions for everything
