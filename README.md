# Code Review App
Domain Driven Designed Application example to show the usage of the different soa-php libraries

This repo contains the code examples explained in the post series:

1. [Implementing a use case (I) - Intro](https://medium.com/@mgonzalezbaile/implementing-a-use-case-i-intro-38c80b4fed0)
2. [Implementing a use case (II) - Command Pattern](https://medium.com/@mgonzalezbaile/implementing-a-use-case-ii-command-pattern-2d49d980e61c)
3. [Implementing a use case (III) - Command Bus](https://medium.com/@mgonzalezbaile/implementing-a-use-case-iii-command-bus-9bff58766d28)
4. [Implementing a use case (IV) — Domain Events (I)](https://medium.com/@mgonzalezbaile/implementing-a-use-case-v-domain-events-i-21549bb87281)
5. [Implementing a use case (IV) — Domain Events (II)](https://medium.com/@mgonzalezbaile/implementing-a-use-case-v-domain-events-ii-22164128ed0f)
6. [Implementing a use case (V) — Given-When-Then Testing Style](https://medium.com/@mgonzalezbaile/implementing-a-use-case-v-given-when-then-testing-style-a17a645b1aa6)
7. To be continued...

# Context
There are 3 bounded contexts:

## User Identity
### Use Cases
- Log User In With Password
- Refresh User Access Token

## Pull Request
### Use Cases
- Create a Pull Request
- Assign a Reviewer to a Pull Request
- Approve a Pull Request
- Merge Pull Request

## Payment
### Use Cases
- Pay money
- Collect Money

## Merge Pull Request Long-Running Process
Merging a Pull Request is an automated process consisting of the next flow:

1) When all the assigned reviewers approve the Pull Request, the Pull Request is marked as mergeable
2) When the Pull Request is marked as mergeable, the system automatically pays to the reviewers the corresponding reward and charges the creator of the Pull Request a fee.
3) When all the payments (collecting and paying the money) are successfully performed the system merges the Pull Request

The Merge Pull Request Process Manager handles this flow.

# Running the code
If you want to play around with the code you just need to:

1) Install `docker` and `docker compose` tools.
2) Run the installation script (it will take long until all dependencies are installed):

`./install.sh`

3) Start mongo and rabbitmq services:

`docker-compose -f docker/infra-docker-compose.yml up`

4) Start bounded context services:

`docker-compose -f docker/services-docker-compose.yml up`

# Testing the app
If you want to test how the application behaves you can send the next HTTP requests:

1) Create a User with `writer` role:

`
curl -i -X POST 
   -H "Content-Type:application/json" 
   -H "Id:http-request-id" 
   -d 
'{
  "email": "writer@email.com",
  "password": "some password",
  "username": "writer",
  "roles": ["writer"]
}' 
 'http://localhost:8081/users/login/password'`
 
2) Create a User with `reviewer` role:

`
curl -i -X POST 
   -H "Content-Type:application/json" 
   -H "Id:http-request-id" 
   -d 
'{
  "email": "reviewer@email.com",
  "password": "some other password",
  "username": "reviewer",
  "roles": ["reviewer", "writer"]
}' 
 'http://localhost:8081/users/login/password'
`

3) Create another User with `reviewer` role:

`
curl -i -X POST 
   -H "Content-Type:application/json" 
   -H "Id:http-request-id" 
   -d 
'{
  "email": "another_reviewer@email.com",
  "password": "some password",
  "username": "another_reviewer",
  "roles": ["reviewer"]
}' 
 'http://localhost:8081/users/login/password'
`

4) Create a Pull Request replacing the `writer_access_token` placeholder with the access token returned when you created the writer in step 1:

`
curl -i -X POST 
   -H "id:ddc42abd-f1b8-4078-8238-a265282bfbfa" 
   -H "Authorization:Bearer {writer_access_token}" 
   -d 
'{"code":"some code"}' 
 'http://localhost:8080/pull-requests'`

5) Assign a Reviewer:

`{writer_access_token}`: the access token returned in step 1.

`{reviewer_id}`: the UUID returned in the `Location` header in step 2.

`{pull_request_id}`: the UUID returned in the `Location` header in step 4.

`
curl -i -X PUT 
   -H "Content-Type:application/json" 
   -H "id:79d4ee69-6e2f-4e6d-b338-9c68ecb88468" 
   -H "Authorization:Bearer {writer_access_token}" 
   -d 
'{
	"reviewer": "{reviewer_id}"
}' 
 'http://localhost:8080/pull-requests/{pull_request_id}/reviewer' `
 
6) Assign another Reviewer:

`{writer_access_token}`: the access token returned in step 1.

`{reviewer_id}`: the UUID returned in the `Location` header in step 3.

`{pull_request_id}`: the UUID returned in the `Location` header in step 4.

`
curl -i -X PUT 
   -H "Content-Type:application/json" 
   -H "id:79d4ee69-6e2f-4e6d-b338-9c68ecb88468" 
   -H "Authorization:Bearer {writer_access_token}" 
   -d 
'{
	"reviewer": "{reviewer_id}"
}' 
 'http://localhost:8080/pull-requests/{pull_request_id}/reviewer' `
 

7) Approve the Pull Request by one Reviewer:

`{reviewer_access_token}`: the access token returned in step 2.

`{pull_request_id}`: the UUID returned in the `Location` header in step 4.

`
curl -i -X PUT 
   -H "Content-Type:application/json" 
   -H "Id:d0e2b3e4-53d0-4010-a486-e1a0cf895bce" 
   -H "Authorization:{reviewer_access_token}" 
   -d 
'' 
 'http://localhost:8080/pull-requests/{pull_request_id}/approve'`

5) Approve the Pull Request by the other Reviwer:

`{reviewer_access_token}`: the access token returned in step 3.

`{pull_request_id}`: the UUID returned in the `Location` header in step 4.

`
curl -i -X PUT 
   -H "Content-Type:application/json" 
   -H "Id:d0e2b3e4-53d0-4010-a486-e1a0cf895bce" 
   -H "Authorization:{reviewer_access_token}" 
   -d 
'' 
 'http://localhost:8080/pull-requests/{pull_request_id}/approve'`

6) At this point, since all reviewers have approved the Pull Request, the Process Manager starts managing the process to charge and pay the money, and then merge the Pull Request.

# Checking the result
You can check the data created by connecting to the Mongo database which is listening at `localhost:27017` without user nor password required. You can also check the queues and exchanges created in RabbitMQ by opening in your browser the URL `http://localhost:15672/#/`. You can login into local RabbitMQ Manager using the user "devuser" with the password "devpass".

# Running Unit Tests
Each Bounded Context has a `run.sh` script, if you want to run the unit tests you just need to go to the Bounded Context folder and execute:

```
cd PullRequest
./run.sh phpunit
```
