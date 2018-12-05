# Code Review App
Domain Driven Designed Application example to show the usage of the different soa-php libraries

# Context
There are 4 bounded contexts:

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
If you want to test how the application behaves you just can send the next HTTP requests:

1) Create a Pull Request:

`
curl -i -X POST \
   -H "id:e0ff1d6d-9035-4974-b711-b9805116abad" \
   -d \
'{"writer":"aaaa", "code":"some code"}' \
 'http://localhost:8080/pull-requests'
`

NOTE: Copy the UUID generated from the `Location` header returned in the Response and replace the `{id}` placeholder from the next CURL calls with the UUID.

2) Assign a Reviewer:

`
curl -i -X PUT \
   -H "Content-Type:application/json" \
   -H "id:eb59b73b-a0d7-4cc8-9d9e-118b11a5f30c" \
   -d \
'{
	"reviewer": "a reviewer"
}' \
 'http://localhost:8080/pull-requests/{id}/reviewer'
 `
 
3) Assign another Reviewer:

`
curl -i -X PUT \
   -H "Content-Type:application/json" \
   -H "id:07d6a393-eb2a-4d00-8351-0e691865fe82" \
   -d \
'{
	"reviewer": "another reviewer"
}' \
 'http://localhost:8080/pull-requests/{id}/reviewer'
` 

4) Approve the Pull Request by one Reviewer:

`
curl -i -X PUT \
   -H "Content-Type:application/json" \
   -H "id:cc8ea97a-8c90-4023-aeb2-f4387c79bee9" \
   -d \
'{
	"approver": "a reviewer"
}' \
 'http://localhost:8080/pull-requests/{id}/approve'
`

5) Approve the Pull Request by the other Reviwer:

`
curl -i -X PUT \
   -H "Content-Type:application/json" \
   -H "id:78ffb2f0-7598-4c9d-abd0-0f06bad29cc2" \
   -d \
'{
	"approver": "another reviewer"
}' \
 'http://localhost:8080/pull-requests/{id}/approve'
`

6) At this point, since all reviewers have approved the Pull Request, the Process Manager starts managing the process to charge and pay the money, and then merge the Pull Request.

# Checking the result
You can check the data created by connecting to the Mongo database which is listening at `localhost:27017` without user nor password required. You can also check the queues and exchanges created in RabbitMQ by opening in your browser the URL `http://localhost:15672/#/`

# Running Unit Tests
Each Bounded Context has a `run.sh` script, if you want to run the unit tests you just need to go to the Bounded Context folder and execute:

```
cd PullRequest
./run.sh phpunit
```