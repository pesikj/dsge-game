# DSGE Game — Simulation for Teaching Financial Literacy

A web-based, multi-player **economic simulation game** built to support teaching the basics of financial literacy at (Czech) high schools and to enable **econometric analysis of player behavior**. Players make periodic decisions about production, consumption, labor, savings and loans across several markets; an administrator configures the economy (production/utility functions, amortization, initial endowments, etc.).

> The model simulates a closed economy (autarky) with **two goods** (consumption & capital), **two production factors** (labor & capital), **money** as medium of exchange, and **financial markets** for savings/loans. Rounds are discrete; prices are found by market-clearing algorithms.

---

## Key Features

* **Round-based markets** for consumption goods, capital goods, labor, and financial assets with market-clearing price determination. Offers/demands are entered as **price-interval orders**; past prices are graphed for reference.
* **Production & scoring**:

  * Production uses an admin-defined function (e.g., Cobb–Douglas with exogenous tech progress). Capital depreciates by an **amortization factor** each round.
  * Players are scored on **consumption** and **leisure** (with time discounting) and can be penalized for **negative cash** or **inactivity**.
* **Financial flows forecast** screen lets players test “what-if” prices and see projected cash-flow and points before they commit.
* **Admin console** to configure the economy: initial endowments, amortization, and **string-defined formulas** for production/utility. Settings can be changed even during the game.
* **Extensible event system** (publish/subscribe style) enabling new game mechanics without invasive changes to existing classes.

---

## Screens & Flow

* **Player UI**: login → overview (cash, stocks, capital in production, points) → production choice → market order entry (price-interval bids/asks) → financial-flow forecast. Past round’s **market price** is shown to aid decisions.
* **Admin UI**: set **initial stocks/cash**, **amortization**, and **production function** (entered as a math expression using `+ - * / ^` with named variables).

---

## Tech Stack

* **Language**: PHP (object-oriented). The codebase implements a lightweight **event-driven architecture** in PHP to approximate language-level events.
* **3rd-party libraries**:

  * **EvalMath** — evaluate admin-entered math expressions for production/utility functions. (BSD)
  * **JpGraph** — server-side charts for market price graphs. (QPL)
  * **TinyMCE** — WYSIWYG editor for editable pages (e.g., the intro). (LGPL)
* **Data model**: Provided as an **ERA model** (entities, attributes, relations) in the thesis appendices; the game persists per-round player states for econometric use.

> The system design emphasizes extendability via **event queues (FIFO)** for events and handlers, inspired by Fells (2006). New game features are added by introducing new classes/listeners and registering them with the dispatcher.

---

## Gameplay Model (Short)

* **Decisions per round**: choose what to produce (consumption vs. capital), how much to **work vs. keep as leisure**, what **orders** to place on each market, and whether to **lend/borrow** on the financial market.
* **Production**: uses the configured function; capital in production **depreciates** each round by the amortization factor.
* **Scoring**: discounted utility from **consumption** and **leisure**; penalties for **negative cash** and **inactivity** (admin-configurable).
* **Forecast tool**: simulate prices to preview **cash-flow** and **points** before submitting orders.

---

## Architecture

* **Event System**

  * Each class that can emit events maintains an **event queue**; handlers are registered in a **handler queue** (both FIFO). On emission, the dispatcher invokes subscribed handlers (loosely coupled “broadcast”).
  * Example core classes include the generic **Controller/Handler** (`Ovladac`) which binds an event name to a target object method; methods receive `(sender, params)`.
* **Core Services**

  * **GUI Manager** (`Spravce_GUI`) composes the XHTML, injects price history of the last round, routes to the requested screen, and enforces access rights.
  * **Validation** ensures orders don’t exceed holdings or allowed work hours; production commands are also checked against maximum hours.

---

## Data & Research

* The game **stores per-round microdata** (cash, inventories, hours worked/sold/bought, loans/savings, etc.) enabling **econometric modeling** and out-of-sample forecasts vs. realized outcomes.
* The thesis includes a full **ERA model** for the database; refer to it when mapping or migrating the schema.

---

## Acknowledgements

Thanks to testers from FEK ZČU who participated in the initial playtests (April 26, 2010).
