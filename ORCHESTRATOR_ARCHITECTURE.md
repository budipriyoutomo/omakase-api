# AI Orchestrator Intelligence - Enhanced Architecture

## 📊 Ringkasan Upgrade

Orchestrator Anda telah di-upgrade dari simple composition menjadi **Intelligent Weighted Orchestration System** dengan:

✅ **4 Specialized Agents** dengan fokus berbeda  
✅ **Sophisticated Agent Router** dengan scoring system  
✅ **Weighted Composition Engine** yang prioritas intelligences secara cerdas  
✅ **Context-Aware Strategy** yang adapt ke campaign goals  

---

## 🏗️ Arsitektur Komponen

### 1. **Specialized Agents** (Baru)

#### SalesConversionAgent
- **Focus**: Maximize conversions melalui CTA optimization
- **Temperature**: 0.5 (precise, consistent)
- **Best for**: "Increase Sales" goals, Strong CTA strategies
- **Prioritizes**:
  - Conversion optimization (primary)
  - CTA prominence (secondary)
  - Audience targeting (tertiary)
  - Visual selling power (quaternary)

#### LuxuryBrandAgent
- **Focus**: Premium positioning & aspiration
- **Temperature**: 0.7 (balanced creativity)
- **Best for**: Luxury mood, High income customers
- **Prioritizes**:
  - Luxury positioning (primary)
  - Aesthetic excellence (secondary)
  - Emotional connection (tertiary)
  - Subtle CTA (quaternary)

#### BrandAwarenessAgent
- **Focus**: Storytelling & brand recall
- **Temperature**: 0.8 (creative)
- **Best for**: "Boost Awareness" goals
- **Prioritizes**:
  - Brand storytelling (primary)
  - Narrative clarity (secondary)
  - Audience alignment (tertiary)
  - Visual storytelling (quaternary)

#### ViralCampaignAgent
- **Focus**: Engagement & shareability
- **Temperature**: 0.9 (highly creative)
- **Best for**: "Create Viral Campaign" goals, Gen Z audience
- **Prioritizes**:
  - Viral potential (primary)
  - Platform optimization (secondary)
  - Audience delight (tertiary)
  - Trendy aesthetics (quaternary)

---

### 2. **Enhanced AgentRouter** (Improved)

**Before**: Semua campaign → MarketingCoach (sama)

**After**: Intelligent scoring system yang evaluate:

```php
Score Calculation:

Luxury Score (max 100):
  - mood='luxury' → +50
  - audience='high income customers' → +30
  - cta='luxury soft cta' → +20
  → Trigger LuxuryBrandAgent if ≥70

Sales Score (max 100):
  - goal='increase sales' → +50
  - cta='strong sales cta' → +35
  - mood≠'luxury' → +15
  → Trigger SalesConversionAgent if ≥80

Viral Score (max 100):
  - goal='create viral campaign' → +60
  - audience='gen z' → +25
  - platform='tiktok|instagram story' → +15
  → Trigger ViralCampaignAgent if ≥75

Awareness Score (max 100):
  - goal='boost awareness' → +60
  - goal='launch new menu|create viral' → +20
  → Trigger BrandAwarenessAgent if ≥65
```

**Fallback**: MarketingCoach (jika semua score < threshold)

---

### 3. **WeightedOrchestrator** (Baru)

Wrapper di atas `PromptOrchestrator` yang menambahkan:

#### Dynamic Weight Calculation

**Default Weights**:
```php
[
  'platform' => 10,
  'audience' => 15,
  'mood' => 20,
  'cuisine' => 15,
  'cta' => 20,
  'goal' => 20,
]
```

**Dynamic Adjustments** berdasarkan goal:

| Goal | Priority Shift |
|------|---|
| **Increase Sales** | cta:35, goal:35, platform:20, mood:5 |
| **Boost Awareness** | mood:30, audience:25, goal:30, cta:10 |
| **Create Viral Campaign** | platform:25, audience:25, goal:30, mood:15, cta:5 |
| **Launch New Menu** | cuisine:35, mood:20, goal:30, cta:10, audience:5 |
| **Luxury Mood** | mood:30, cta:15, audience:20 |
| **Gen Z Audience** | platform:30, mood:15 |
| **High Income** | mood:30, platform:10 |

#### Composition Reordering

Intelligences diurutkan berdasarkan weight (tertinggi duluan):

```json
{
  "composition": [
    "high-converting layout",        // goal focus
    "strong call-to-action",         // cta focus
    "mobile-first storytelling",     // platform focus
    "trendy aesthetic",              // audience focus
    "luxury plating",                // cuisine focus
    "premium atmosphere"             // mood focus
  ],
  "weights": {
    "cta": 35,
    "goal": 35,
    "platform": 20,
    "cuisine": 10,
    "audience": 10,
    "mood": 5
  },
  "metadata": {
    "primary_focus": "Goal Focus",
    "weight_strategy": "Conversion Optimization"
  }
}
```

---

## 🔄 Execution Flow

```
CampaignPayloadDTO
    ↓
PromptGenerationService.generate()
    ↓
├─→ WeightedOrchestrator.build(dto)
│   ├─→ PromptOrchestrator.build() [base composition]
│   ├─→ calculateWeights(dto) [dynamic weights]
│   ├─→ prioritizeComposition() [reorder by weight]
│   └─→ Orchestration array (with weights + metadata)
│
├─→ AgentRouter.resolve(dto)
│   ├─→ calculateAgentScore(dto)
│   └─→ Specialized Agent class
│
├─→ Agent.prompt(json_encoded orchestration)
│   └─→ Enhanced prompt generation
│
└─→ Result array
    ├─→ agent (SalesConversionAgent|LuxuryBrandAgent|...)
    ├─→ orchestration (with weights)
    └─→ enhanced_prompt (AI generated)
```

---

## 📋 Contoh Use Cases

### Case 1: Sales Campaign untuk Gen Z (TikTok)
```php
$dto = new CampaignPayloadDTO(
    campaignType: 'Sales',
    goal: 'Increase Sales',          ← Sales Agent trigger
    platform: 'TikTok',
    audience: 'Gen Z',
    mood: 'Energetic',
    // ...
);
```

**Router Decision**: SalesConversionAgent (sales: 85/100)

**Weights**:
- cta: 35 (primary)
- goal: 35 (primary)
- platform: 20
- audience: 10
- cuisine: 10
- mood: 5

**Agent Behavior**: Fokus conversion + urgency + platform optimization

---

### Case 2: Luxury Restaurant Campaign
```php
$dto = new CampaignPayloadDTO(
    campaignType: 'Brand',
    goal: 'Boost Awareness',
    audience: 'High Income Customers',
    mood: 'Luxury',                   ← Luxury trigger
    // ...
);
```

**Router Decision**: LuxuryBrandAgent (luxury: 80/100)

**Weights**:
- mood: 30 (primary)
- audience: 25 (secondary)
- goal: 30 (primary)
- cuisine: 10
- platform: 5

**Agent Behavior**: Cinematic storytelling + premium aesthetics + minimal CTA

---

### Case 3: Viral Campaign
```php
$dto = new CampaignPayloadDTO(
    goal: 'Create Viral Campaign',    ← Viral trigger
    audience: 'Gen Z',
    platform: 'TikTok',
    // ...
);
```

**Router Decision**: ViralCampaignAgent (viral: 100/100)

**Weights**:
- platform: 25 (primary)
- audience: 25 (primary)
- goal: 30 (primary)
- mood: 15
- cuisine: 5
- cta: 5

**Agent Behavior**: Trending formats + engagement hooks + platform-native optimizations

---

## 🎯 Key Improvements

### Before vs After

| Aspek | Before | After |
|-------|--------|-------|
| **Agents** | 1 (all same) | 4 specialized |
| **Routing Logic** | Simple if/else | Weighted scoring system |
| **Intelligence Order** | Fixed (platform→audience→mood→...) | Dynamic priority-based |
| **Agent Focus** | General | Specialized by campaign type |
| **Context Awareness** | Minimal | Sophisticated multi-factor |
| **Weight System** | None | Full weighted composition |
| **Metadata** | None | Strategy + focus info included |

---

## 🚀 Future Enhancement Ideas

1. **Feedback Loop**: Track campaign performance → adjust weights automatically
2. **A/B Testing**: Compare multiple weight strategies per campaign type
3. **ML-Powered Weights**: Learn optimal weights from historical campaign data
4. **Multi-Language Support**: Adapt agents untuk different languages
5. **Real-time Optimization**: Adjust weights mid-campaign based on engagement metrics
6. **Agent Chaining**: Combine multiple agents untuk more complex scenarios
7. **Conflict Resolution**: Handle contradictory intelligences intelligently

---

## 📝 Testing Checklist

```php
// Test 1: Sales conversion routing
$dto = CampaignPayloadDTO with goal='Increase Sales'
→ Verify SalesConversionAgent selected
→ Verify cta weight = 35, goal weight = 35

// Test 2: Luxury positioning
$dto = CampaignPayloadDTO with mood='Luxury'
→ Verify LuxuryBrandAgent selected
→ Verify mood weight = 30

// Test 3: Viral campaign
$dto = CampaignPayloadDTO with goal='Create Viral Campaign'
→ Verify ViralCampaignAgent selected
→ Verify platform weight = 25, audience weight = 25

// Test 4: Weight calculation
→ Verify weights sum properly
→ Verify composition reordering by weight

// Test 5: Agent temperature differences
→ SalesConversionAgent: 0.5 (precise)
→ ViralCampaignAgent: 0.9 (creative)
```

---

## 📚 Class Diagram

```
┌──────────────────────────────────┐
│   PromptGenerationService        │
│  ├─ orchestrator                 │
│  ├─ router                       │
│  └─ generate(dto)                │
└──────────┬───────────────────────┘
           │
           ├──→ WeightedOrchestrator ─→ PromptOrchestrator
           │         ├─ calculateWeights()
           │         └─ prioritizeComposition()
           │
           └──→ AgentRouter
                ├─ resolve(dto)
                └─ calculateAgentScore()
                    ├──→ SalesConversionAgent
                    ├──→ LuxuryBrandAgent
                    ├──→ BrandAwarenessAgent
                    ├──→ ViralCampaignAgent
                    └──→ MarketingCoach (fallback)
```

---

Ini adalah **intelligent orchestration system** yang adapt ke campaign context dan deliver specialized prompts untuk setiap agent!
