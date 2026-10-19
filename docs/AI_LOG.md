# AI usage log

> Required by the project rules: one section per milestone with the AI tools used, the most
> important prompts, and **at least two cases where the AI was wrong** – what was wrong,
> how it was noticed, and how it was fixed.

## Milestone 1 – Setup, static frontend, ERD

**Tools used:** Claude (chat, Anthropic) for suggestions during planning topic ideas, advice on best practices during the database design. Additionally used for generating static HTML views after detailed prompts. Claude chat, ChatGPT, Gemini and my locally installed LLM (qwen2.5-coder:7b) were used for explanations and for summarizing HTML, PokeAPI and jQuery docs. On several occasions I asked several different models the same questions just to see how the answers differ. No code-completion tool (Copilot/Cursor) was used. Everything was tested by me in the browser (Chrome Developer Tools). This log and parts of `AGENTS.md` were first drafted by Claude from our chat history and then checked and edited by me.

**Most important prompts**

- "Can you thoroughly analyze the database for weaknesses according to the list of common ones I pasted earlier?" plus my own questions ("what is the point of team_members?", "why do I need pokemon_types and types in your version of the design?") → schema reduced to 8 tables.
- "Explain in detail how a single page app works and what the code looks like, so I can make it myself." → used while initially getting a grasp on how the project should work.
- "Using the Bootswatch Darkly theme, make the static pages for login, register."

**What I decided myself (not the AI)**

- The topic, the guest/trainer/admin split, the main crud entities in my database design and that live PokéAPI details are only for logged-in users (to lower potential over-use of PokéAPI).
- The overall structure for each page, as well as the Darkly theme, and other styling decisions.

**How I used and checked AI output**

- I ran every file in the browser and read the Console / Network tab when something failed.
- The type-weakness numbers on the sample analysis page were fully generated for now, but in future, functional implementation I will carefully design the logic myself with no AI interference according to the logic of pokemon games. AI will at that point be used to check whether my logic is sound, and I will also use other existing calculators I find online to double check several example cases.
- Anything the AI explained (SPA, jQuery vs plain JS, spapp, ERD notation) I had it re-explain until I could say it in my own words. If I could not understand well enough just from the AI explanation, I followed up by diving into reading documentation, watching youtube video explanations and reading posts on the topics at hand.

**Cases where the AI was wrong**

1. The first database design I gave showed AI had 7 tables (only 4 crud entities), which AI helped me improve but provided a new design with 10 tables, including `game_generations` and `pokemon_types`. I asked what the purpose of those tables was and could not understand why I needed them, therefore could not justify them. The generation became a column on `games`, and the types became `type1_id` / `type2_id` on `pokemon`.
2. Since I used the Chat version of Claude from Anthropic most, I had issues keeping it up to date (mostly because I wasn't planning on using it much). Often I would make significant changes to my project and didn't keep it up to date with my project architecture, nor did I provide it with context about the project and the project requirements. Although this could be attributed as my mistake, I believe AI often makes mistakes in this grey zone as it makes many "interesting" suggestions which might lead less experienced developers such as me astray easily. I also sometimes asked my local AI model (qwen2.5-coder:7b) the same questions and it's responses can definitely be considered misleading sometimes. One example of a suggestion which I would consider misguiding or mistaken is:
   - prompt: "I am doing a SPA website project for a course and planning on using FlightPHP, MySQL, and bootstrap as the base for my tech stack. So far I've only done work with Django and python for backend so I want you to explain the differences between Django and FlightPHP and give me a bare-bones plan that could help anyone with their first PHP website."
   - answer: "Since you've already worked with Django, I'd recommend sticking with it for this project. FlightPHP is much more lightweight, meaning you'll have to implement more functionality yourself, including database handling and authentication. Django already provides these features out of the box.
     For a SPA, you can simply use Django as a JSON API backend with Bootstrap and JavaScript on the frontend. Switching to PHP would add a learning curve without providing much benefit for your use case.
     In case you want to stick with PHP
   1. Install PHP, Composer, and MySQL.
   2. Create a FlightPHP project and define your routes.
   3. Connect to MySQL and create your database tables.
   4. Use Bootstrap for styling and JavaScript's fetch() to communicate with your PHP backend.
   5. Implement registration, login, and PHP sessions.
   6. Test each feature before integrating everything into your SPA."

**Things that did not work (process)**

- Asking the AI to give me a complete detailed explanation of all the topics that I will be learning during this project (after I explained the project and tech stack). Either I was not capable of cramming in so much information quickly, or the AI abstractly explained too many concepts in an attempt to summarize it all in one answer message. Now I look up things myself and only ask for detailed explanations of very specific concepts I am covering at the moment.
