// 简单交互：模块筛选/小动效预留
document.addEventListener('DOMContentLoaded', () => {
  const filterInput = document.querySelector('#module-filter');
  const tagEls = Array.from(document.querySelectorAll('.tag'));
  const cards = Array.from(document.querySelectorAll('.card'));

  const getCardMeta = (card) => {
    const meta = card.querySelector('.meta');
    const tags = (meta?.dataset.tags || '').split(',').map(s => s.trim()).filter(Boolean);
    const group = meta?.dataset.group || '';
    const text = card.textContent.toLowerCase();
    return { tags, group, text };
  };

  const activeTags = new Set();
  const applyFilter = () => {
    const q = (filterInput?.value || '').trim().toLowerCase();
    // 收集分组可见性
    const visibleByGroup = new Map();
    cards.forEach(card => {
      const { tags, group, text } = getCardMeta(card);
      let visible = true;
      if (q) visible = text.includes(q);
      if (visible && activeTags.size > 0) {
        visible = tags.some(t => activeTags.has(t));
      }
      card.style.display = visible ? '' : 'none';
      if (visible) {
        visibleByGroup.set(group, (visibleByGroup.get(group) || 0) + 1);
      }
    });
    // 更新分组标题（按需渲染）
    renderGroupTitles(visibleByGroup);
  };

  const renderGroupTitles = (visibleByGroup) => {
    // 先移除旧标题
    document.querySelectorAll('.group-title').forEach(el => el.remove());
    // 在每个分组的第一个可见卡片前插入标题
    const handledGroups = new Set();
    cards.forEach(card => {
      if (card.style.display === 'none') return;
      const { group } = getCardMeta(card);
      if (!group || handledGroups.has(group)) return;
      const title = document.createElement('div');
      title.className = 'group-title';
      title.textContent = group;
      card.parentElement.insertBefore(title, card);
      handledGroups.add(group);
    });
  };

  tagEls.forEach(tagEl => {
    tagEl.addEventListener('click', () => {
      const tag = tagEl.dataset.tag;
      if (!tag) return;
      if (activeTags.has(tag)) {
        activeTags.delete(tag);
        tagEl.classList.remove('active');
      } else {
        activeTags.add(tag);
        tagEl.classList.add('active');
      }
      applyFilter();
    });
  });

  if (filterInput) {
    filterInput.addEventListener('input', applyFilter);
  }

  // 初次渲染分组标题
  applyFilter();
});