<li>
    <div class="ft-branch-card">
        <div class="ft-person-card">
            <div class="ft-person-top">
                @if($node['member']->photo_path)
                    <span class="ft-photo-hover">
                        <img class="ft-avatar" src="{{ asset($node['member']->photo_path) }}" alt="{{ $node['member']->name }}">
                        <span class="ft-photo-preview">
                            <img src="{{ asset($node['member']->photo_path) }}" alt="{{ $node['member']->name }} full photo">
                        </span>
                    </span>
                @else
                    <span class="ft-avatar-placeholder"><i class="bi bi-person-fill"></i></span>
                @endif
                <div>
                    <div class="ft-name">{{ $node['member']->name }}</div>
                    <div class="ft-meta">{{ $node['member']->date_of_birth?->format('d M Y') ?: 'DOB not added' }}</div>
                </div>
            </div>
            @if($node['member']->relationship_to_other && $node['member']->relatedMember)
                <div class="ft-badge">{{ ucfirst($node['member']->relationship_to_other) }} of {{ $node['member']->relatedMember->name }}</div>
            @endif
        </div>

        @if(!empty($node['spouse']))
            <div class="ft-link-heart"><i class="bi bi-heart-fill"></i></div>
            <div class="ft-person-card ft-spouse-card">
                <div class="ft-person-top">
                    @if($node['spouse']->photo_path)
                        <span class="ft-photo-hover">
                            <img class="ft-avatar" src="{{ asset($node['spouse']->photo_path) }}" alt="{{ $node['spouse']->name }}">
                            <span class="ft-photo-preview">
                                <img src="{{ asset($node['spouse']->photo_path) }}" alt="{{ $node['spouse']->name }} full photo">
                            </span>
                        </span>
                    @else
                        <span class="ft-avatar-placeholder"><i class="bi bi-person-fill"></i></span>
                    @endif
                    <div>
                        <div class="ft-name">{{ $node['spouse']->name }}</div>
                        <div class="ft-meta">{{ $node['spouse']->date_of_birth?->format('d M Y') ?: 'DOB not added' }}</div>
                    </div>
                </div>
                <div class="ft-badge ft-badge-love">Partner</div>
            </div>
        @endif
    </div>

    @if(!empty($node['children']))
        <ul class="ft-children">
            @foreach($node['children'] as $child)
                @include('frontend.partials.family-tree-node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
