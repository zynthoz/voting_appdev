---
name: Election Voting System
description: A clean, civic, and highly trustworthy role-based election voting platform.
colors:
  primary: "#1a365d"
  primary-light: "#2b6cb0"
  neutral-bg: "#fafafa"
  neutral-surface: "#ffffff"
  neutral-border: "#e2e8f0"
  text-primary: "#1a202c"
  text-secondary: "#4a5568"
  success: "#2f855a"
  error: "#9b2c2c"
  warning: "#c05621"
typography:
  display:
    fontFamily: "Outfit, Inter, sans-serif"
    fontSize: "2.25rem"
    fontWeight: 700
    lineHeight: 1.2
  body:
    fontFamily: "Inter, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.5
rounded:
  sm: "4px"
  md: "8px"
  lg: "12px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.neutral-surface}"
    rounded: "{rounded.sm}"
    padding: "8px 16px"
  button-primary-hover:
    backgroundColor: "{colors.primary-light}"
  input-field:
    backgroundColor: "{colors.neutral-surface}"
    textColor: "{colors.text-primary}"
    rounded: "{rounded.sm}"
    padding: "10px 14px"
---

# Design System: Election Voting System

## 1. Overview

**Creative North Star: "The Civic Ledger"**

The visual system is designed to convey trust, clarity, and official purpose. It treats voting as a serious, civic duty, avoiding consumer-oriented trendiness. Layouts prioritize clean spatial structure, clear typographic hierarchy, and structured grids.

**Key Characteristics:**
- Restrained color strategy focusing on tinted neutrals and a singular, authoritative blue accent.
- Clear typographic separation with bold headings (Outfit) and highly readable body text (Inter).
- Flat, high-contrast UI with subtle elevation changes to support functional interactive paths.

## 2. Colors

A restrained palette of deep, trustworthy blues combined with slate neutrals and clear semantic colors for feedback.

### Primary
- **Civic Navy** (#1a365d / oklch(0.28 0.08 250)): The authoritative anchor color of the system. Used for core navigation, active states, and primary actions.

### Primary Light
- **Voter Blue** (#2b6cb0 / oklch(0.48 0.16 245)): The interactive accent, used for hover states and secondary elements.

### Neutral
- **Slate Text** (#1a202c / oklch(0.18 0.01 250)): Canonical dark text color.
- **Muted Text** (#4a5568 / oklch(0.42 0.01 250)): Secondary text, metadata, and labels.
- **Cool Border** (#e2e8f0 / oklch(0.93 0.01 250)): Subtle boundary lines for tables and forms.
- **Pristine Surface** (#ffffff / oklch(1.0 0.0 0)): Card and sheet backgrounds.
- **Civic Background** (#fafafa / oklch(0.98 0.003 250)): Primary background for pages.

### Named Rules
**The Restrained Hue Rule.** Primary accent color usage must not exceed 10% of any screen layout. It is reserved strictly for primary interactive anchors.

## 3. Typography

**Display Font:** Outfit
**Body Font:** Inter

### Hierarchy
- **Display** (Bold (700), 2.25rem, 1.2): Main page headers and system titles.
- **Headline** (Semi-Bold (600), 1.5rem, 1.3): Component group headers and section titles.
- **Title** (Medium (500), 1.25rem, 1.4): Card titles, data table column headers.
- **Body** (Regular (400), 1rem, 1.5): Standard prose, table values, input text.
- **Label** (Medium (500), 0.875rem, 1.4): Form input descriptions, helper text, system badges.

## 4. Elevation

The system is flat by default to enhance high-contrast legibility. Depth is communicated primarily through distinct background coloring and borders, with shadows reserved for overlays.

### Shadow Vocabulary
- **Interactive Shadow** (box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)): Used on hover for action items.
- **Overlay Shadow** (box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)): Reserved strictly for system modals.

## 5. Components

### Buttons
- **Shape:** Soft-cornered (4px)
- **Primary:** Background Civic Navy, text white.
- **Hover:** Voter Blue. Focus states have a 2px offset border.

### Cards / Containers
- **Corner Style:** Medium (8px)
- **Background:** Pristine Surface.
- **Border:** 1px Cool Border.
- **Internal Padding:** Large (24px) spacing.

### Inputs / Fields
- **Style:** 1px Cool Border with Pristine Surface background.
- **Focus:** 1px border transition to Voter Blue with 2px blue ring.

### Navigation
- **Style:** High-contrast sidebar using Civic Navy background. Active links feature a high-contrast badge and indicator dot. Mobile view collapses sidebar to a clean top header.

## 6. Do's and Don'ts

### Do:
- **Do** use OKLCH colors for dynamic styling and keep contrast ratios above 4.5:1.
- **Do** align data tables to a rigid 8px grid.
- **Do** keep forms single-columned with labels positioned above fields.

### Don't:
- **Don't** use border-left greater than 1px as a colored accent stripe on cards or callouts.
- **Don't** use gradient text or glassmorphism effects.
- **Don't** use the hero-metric layout (e.g. big numbers over small text labels).
- **Don't** use modals as a first option for interactions; favor inline actions and confirmation prompts.
