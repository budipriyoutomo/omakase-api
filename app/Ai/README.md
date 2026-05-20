# AI Visual Orchestration Architecture

This backend is now organized around two intelligence layers:

1. Campaign Intelligence
   - Converts business inputs into structured psychology and strategy.
   - Owns campaign goal, conversion priority, brand positioning, audience energy, CTA strength, platform behavior, marketing energy, and tone.
   - Does not generate prompts.

2. Visual Intelligence
   - Converts campaign psychology into commercial visual direction.
   - Owns composition, framing, camera angle, lighting, food styling, typography-safe layout, CTA-safe spacing, hierarchy, realism level, and platform-native framing.
   - Routes by visual/composition signals, not sales/viral/awareness marketing categories.

## Flow

`GenerationService`
creates the generation record and dispatches `ProcessGenerationJob` on the `ai-generation` queue.

`ProcessGenerationJob`
builds `CampaignPayloadDTO`, then runs:

1. `PromptGenerationPipeline`
2. `CampaignIntelligenceOrchestrator`
3. `VisualCompositionOrchestrator`
4. `VisualDirectionRouter`
5. visual director refinement
6. `CommercialPromptRenderer`
7. `ImageGenerationPipeline`
8. provider prediction polling on the `ai-polling` queue
9. `StoragePipeline`
10. analytics-ready metadata persistence

## Responsibility Rules

- Providers communicate with external APIs.
- Orchestrators only build structured intelligence.
- Renderers convert structured orchestration into AI-native prompts.
- Agents refine cinematic visual language only.
- Campaign and Visual intelligence are separate DTO layers.
- Prompt generation is prompt rendering, not string concatenation scattered across services.

## Provider Expansion

Add future providers by implementing:

- `Contracts\ImageProvider` for image creation.
- `Contracts\ImagePredictionProvider` for async polling.

Provider candidates include Gemini, OpenAI, Anthropic, Replicate, Fal AI, Banana.dev, and Ideogram. Keep all HTTP clients and provider-specific payloads inside provider classes.

## Future Pipelines

The persisted `orchestration`, `ai_metadata.visual_orchestration_lifecycle`, and `metadata.visual_ai` structures are designed for:

- multi-image variants
- upscale jobs
- prompt versioning
- analytics learning loops
- visual performance tracking
- provider/model comparison
- campaign-to-visual optimization
