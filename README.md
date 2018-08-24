# ApiLoader

A data loader listener to be attached to ApiController events.

## 1)  Subject

The data charging is an essential part of each app and web projects. As such, the API part of Kairos project have to be able to load, store, validate the client data, and so more.

The ApiLoader system as itself is in charge of the data loading part of this feature. It has to work with a large quantity of driver, adapter or web-services clients. By the way, the genericity of this segment is the key feature. By default, it won't implement any loading logic but only a general paradigm.

At a first level, the component will have to process two well-known operations:
 * load a specific item
 * load a set of items

To rely upon the ApiController library, the ApiLoader will define it as a dependency. Then it became possible to integrate the event dispatching logic. This logic will allow the developers to attach some query building extension. These extensions will work with a query building event instead of the top ApiController level ProcessEvent.

As the original event contains references to request, possibly needed by extensions, the new one will offer access to it.

## 2) Class architecture

The API loader system is a placeholder for more specific data access objects (DAO). To follow this principle the element cannot implement a data loading algorithm but will contribute to offer an abstract loading workflow to be inherited by specific DAO.

The abstract loader will implement three methods, as a unique entry point for the two basics loading operations. These two are :
 * loadCollection
 * loadItem

The element will have to manage a logging system, to ensure the traceability of the system.

## 3) Dependency description and use into the element

A the time of writing, the API controller is designed to have three production dependencies as:

 * psr/log
 * symfony/event-dispatcher
 * kairos-project/api-controller

### 3.1) psr/log

The debugging and error retracement in each project parts is currently a fundamental law in development and it's missing is part of the OWASP top ten threats.

As defined by the third PHP standard reference, the logger components have to implement a specific interface. By the way, the logging system will be usable by each existing frameworks.

### 3.2) symfony/event-dispatcher

The API loader system is designed to be easily extendable and will implement an event dispatching system, allowing the attachment and separation of logic by priority.

### 3.3) kairos-project/api-controller

The API loader is made to be used by APIs and the generic system into kairos project is the API controller. This system offer access to specialized workflow events.

The loader will define the controller component as a dependency to make use of the workflow events.

## 4) Implementation specification

As explained in section two, the abstract class will implement the 'loadCollection' and 'loadItem' methods.

#### 4.1) Dependency injection specification

The instance will receive the logger instance and the event dispatcher at the instantiation directly in the constructor.

For a configuration capability, two event names can be provided to define the query building dispatch's events. These elements will have a default value, define by constant.

A query result key can also be defined.

#### 4.2) loadCollection method algorithm

The loadCollection method is designed to find and load a collection of item.

```txt
We assume to receive the process event from the parameters.
We assume to receive the event name from the parameters.
We assume to receive the event dispatcher from the parameters.

Get a queryBuilding event from the getQueryBuildingEvent by offer the initial event.
Provide the event to instanciateQueryBuilder, and inject a new queryBuilder.
Provide the event to configureQueryForCollection. This step configures the queryBuilder.
Dispatch a query building event name. This action allows the modification of the query by some attached extensions.
Execute the query for collection and insert the result into the original event, at a defined key.
```

#### 4.3) loadItem method algorithm

The loadItem method is designed to find and load a specific item.

```txt
We assume to receive the process event from the parameters.
We assume to receive the event name from the parameters.
We assume to receive the event dispatcher from the parameters.

Get a queryBuilding event from the getQueryBuildingEvent by offer the initial event.
Provide the event to instanciateQueryBuilder, and inject a new queryBuilder.
Provide the event to configureQueryForItem. This step configures the queryBuilder.
Dispatch a query building event name. This action allows the modification of the query by some attached extensions.
Execute the query for an item and insert the result into the original event, at a defined key.
```

#### 4.4) Event specification

The query building event, defined by the library is the transport element of the initial process event and the query builder. It will have to inherit the base event logic and store the process event at instantiation and provide a setQuery method.

The event will have to offer getters, to retrieve the stored elements. A getQuery and a getProcessEvent will be part of the component operations.

## 5) Usage

The API loader only offers an abstract class and cannot be used itself.